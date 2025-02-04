<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * The assignsubmission_edulegit submission manager class.
 *
 * @package   assignsubmission_edulegit
 * @author    Alex Crosby <developer@edulegit.com>
 * @copyright @2024 EduLegit.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignsubmission_edulegit;

/**
 * Class edulegit_submission_manager
 *
 * This class manages the interaction between Moodle submissions and the EduLegit system.
 */
class edulegit_submission_manager {

    /**
     * @var edulegit_config Configuration object for the EduLegit plugin.
     */
    protected edulegit_config $config;

    /**
     * @var edulegit_client Client for communicating with the EduLegit API.
     */
    protected edulegit_client $client;

    /**
     * @var edulegit_submission_repository Repository for handling submission data.
     */
    protected edulegit_submission_repository $repository;

    /**
     * Constructor for the submission manager.
     *
     * @param edulegit_config $config Configuration object for the EduLegit plugin.
     * @param edulegit_client $client Client for communicating with the EduLegit API.
     * @param edulegit_submission_repository $repository Repository for handling submission data.
     */
    public function __construct(edulegit_config $config, edulegit_client $client,
            edulegit_submission_repository $repository) {
        $this->config = $config;
        $this->client = $client;
        $this->repository = $repository;
    }

    /**
     * Initializes a submission with EduLegit.
     *
     * @param object $submission The Moodle submission object.
     * @param array $options Additional options, such as user information.
     * @return edulegit_submission_entity|null The EduLegit submission entity or null if initialization fails.
     */
    public function init($submission, $options = []): ?edulegit_submission_entity {
        if (empty($submission) || empty($submission->assignment) || empty($submission->userid)) {
            return null;
        }
        $edulegitsubmission = $this->get_or_create_edulegit_submission($submission);
        if (!$edulegitsubmission) {
            return null;
        }

        $assignment = $this->repository->get_assignment_info($submission->assignment);

        $autoplagiarismcheck = (bool) $this->config->get_plugin_or_global_config('enable_plagiarism');
        $autoaicheck = (bool) $this->config->get_plugin_or_global_config('enable_ai');
        $mustrecordevents = (bool) $this->config->get_plugin_or_global_config('enable_plagiarism');
        $mustrecordscreen = (bool) $this->config->get_plugin_or_global_config('enable_screen');
        $mustrecordcamera = (bool) $this->config->get_plugin_or_global_config('enable_camera');
        $mustrecognizeattentionmap = (bool) $this->config->get_plugin_or_global_config('enable_attention');

        $data = [
                'meta' => [
                        'callbackUrl' => (string) $this->build_callback_url(),
                        'moodle' => $this->config->get_release(),
                        'plugin' => $this->config->get_plugin_release(),
                ],
                'user' => [
                        'externalId' => (int) $submission->userid,
                        'email' => $options['user']?->email ?? null,
                        'firstName' => $options['user']?->firstname ?? null,
                        'lastName' => $options['user']?->lastname ?? null,
                ],
                'taskUser' => [
                        'externalId' => (int) $edulegitsubmission->id,
                ],
                'task' => [
                        'externalId' => (int) $assignment->id,
                        'title' => $assignment->name ?: $assignment->id,
                        'text' => $assignment->activity ?: ($assignment->intro ?? ''),
                        'description' => ($assignment->intro ?? ''),
                        'startedAt' => $assignment->allowsubmissionsfromdate ?? null,
                        'finishedAt' => $assignment->duedate ?? ($assignment->gradingduedate ?? null),
                ],
                'course' => [
                        'externalId' => (int) $assignment->course,
                        'title' => $assignment->course_fullname ?: $assignment->course_shortname,
                        'text' => $assignment->course_summary ?? '',
                        'startedAt' => $assignment->course_startdate ?? null,
                        'finishedAt' => $assignment->course_enddate ?? null,
                        'setting' => [
                                'autoPlagiarismCheck' => $autoplagiarismcheck,
                                'autoAiCheck' => $autoaicheck,
                                'mustRecordEvents' => $mustrecordevents,
                                'mustRecordScreen' => $mustrecordscreen,
                                'mustRecordCamera' => $mustrecordcamera,
                                'mustRecognizeAttentionMap' => $mustrecognizeattentionmap,
                        ],
                ],
        ];

        $response = $this->client->init_assignment($data);

        $payload = $response->get_payload();
        $responseobject = $payload->data ?? null;

        // Handle API service errors.
        if (!$response->get_success() || empty($payload->success) || !$responseobject) {
            $error = $payload->error ?? ($response->get_error() ?: 'EduLegit service error.');

            $edulegitsubmission->status = 0;
            $edulegitsubmission->error = $error;

            return $this->repository->update_submission($edulegitsubmission) ? $edulegitsubmission : null;
        }

        return $this->sync_submission_edulegit_response($submission, $responseobject);
    }

    /**
     * Builds the callback URL for the webhook handler.
     *
     * @return \moodle_url The constructed callback URL.
     */
    private function build_callback_url(): \moodle_url {
        return new \moodle_url('/webservice/rest/server.php', [
                'wstoken' => $this->parse_plugin_wstoken(),
                'wsfunction' => 'assignsubmission_edulegit_webhook_handler',
                'moodlewsrestformat' => 'json',
        ]);
    }

    /**
     * Retrieves the web service token for the plugin.
     *
     * @return string The web service token or an empty string if not set.
     */
    private function parse_plugin_wstoken(): string {
        return $this->config->get_plugin_or_global_config('ws_token') ??
                ($this->config->get_plugin_or_global_config('wstoken') ?? '');
    }

    /**
     * Synchronizes a submission with EduLegit by its id.
     *
     * @param int $submissionid The ID of the submission to synchronize.
     * @return edulegit_submission_entity|null The EduLegit submission entity or null if synchronization fails.
     */
    public function sync(int $submissionid): ?edulegit_submission_entity {
        $edulegitsubmission = $this->repository->get_submission($submissionid);

        return $edulegitsubmission;
    }

    /**
     * Retrieves or creates an EduLegit submission entity.
     *
     * @param object $submission The Moodle submission object.
     * @return edulegit_submission_entity|null The EduLegit submission entity or null if creation fails.
     */
    private function get_or_create_edulegit_submission($submission): ?edulegit_submission_entity {
        $edulegitsubmission = $this->repository->get_submission($submission->id);
        if ($edulegitsubmission) {
            return $edulegitsubmission;
        }

        $edulegitsubmission = new edulegit_submission_entity();
        $edulegitsubmission->submission = $submission->id;
        $edulegitsubmission->assignment = $submission->assignment;

        $edulegitsubmission->id = $this->repository->insert_submission($edulegitsubmission);

        if (!$edulegitsubmission->id) {
            return null;
        }

        return $edulegitsubmission;
    }

    /**
     * Synchronizes the EduLegit submission entity with the data from response.
     *
     * @param object $submission The Moodle submission object.
     * @param object $data The data received from the EduLegit response.
     * @return edulegit_submission_entity|null The updated EduLegit submission entity or null if update fails.
     */
    private function sync_submission_edulegit_response($submission, $data): ?edulegit_submission_entity {
        $edulegitsubmission = $this->get_or_create_edulegit_submission($submission);
        if (!$edulegitsubmission) {
            return null;
        }

        $edulegitsubmission->title = $data->taskDocument->title ?? null;
        $edulegitsubmission->content = $data->taskDocument->content ?? null;
        $edulegitsubmission->documentid = $data->taskDocument->id ?? 0;
        $edulegitsubmission->taskid = $data->task->id ?? ($data->taskUser->taskId ?? 0);
        $edulegitsubmission->taskuserid = $data->taskUser->id ?? ($data->taskUser->taskUserId);
        $edulegitsubmission->url = $data->sharedDocument->viewUrl ?? ($data->sharedDocument->pdfUrl ?? null);
        $edulegitsubmission->authkey = $data->sharedDocument->authKey ?? null;
        $edulegitsubmission->score = $data->taskDocument->score ?? null;
        $edulegitsubmission->plagiarism = $data->taskDocument->plagiarism ?? null;
        $edulegitsubmission->airate = $data->taskDocument->aiAverageProbability ?? null;
        $edulegitsubmission->aiprobability = $data->taskDocument->aiProbability ?? null;
        $edulegitsubmission->baseurl = $data->baseUrl ?? null;
        $edulegitsubmission->userid = $data->user->id ?? null;
        $edulegitsubmission->userkey = $data->user->loginTimeToken ?? null;

        $edulegitsubmission->error = null;
        $edulegitsubmission->status = 1;

        return $this->repository->update_submission($edulegitsubmission) ? $edulegitsubmission : null;
    }

    /**
     * Deletes an assignment by its ID.
     *
     * @param int $assignmentid The ID of the assignment to delete.
     * @return bool True if the assignment was deleted successfully, false otherwise.
     */
    public function delete_assignment($assignmentid): bool {
        $this->delete_remote_assignment($assignmentid);
        return $this->repository->delete_assignment($assignmentid);
    }

    /**
     * Deletes a submission by its ID.
     *
     * @param int $submissionid The ID of the submission to delete.
     * @return bool True if the submission was deleted successfully, false otherwise.
     */
    public function delete_submission($submissionid): bool {
        $this->delete_remote_submission($submissionid);
        return $this->repository->delete_submission($submissionid);
    }

    /**
     * Deletes all remote user tasks associated with a specific assignment.
     *
     * @param int $assignmentid The ID of the assignment whose user tasks should be deleted.
     * @return bool True if the remote assignment user tasks were deleted successfully, false otherwise.
     */
    private function delete_remote_assignment($assignmentid): bool {
        $usertaskids = $this->repository->get_assignment_task_user_ids($assignmentid);
        return $this->delete_remote_assigment_user_tasks($usertaskids);
    }

    /**
     * Deletes all remote user tasks associated with a specific submission.
     *
     * @param int $submissionid The ID of the submission whose user tasks should be deleted.
     * @return bool True if the remote submission user tasks were deleted successfully, false otherwise.
     */
    private function delete_remote_submission($submissionid): bool {
        $usertaskids = $this->repository->get_submission_task_user_ids($submissionid);
        return $this->delete_remote_assigment_user_tasks($usertaskids);
    }

    /**
     * Deletes remote user tasks based on their IDs.
     *
     * @param array|int[] $usertaskids An array of user task IDs to delete.
     * @return bool True if the tasks were deleted successfully, false otherwise.
     */
    private function delete_remote_assigment_user_tasks($usertaskids): bool {
        if (!$usertaskids) {
            return false;
        }
        try {
            $response = $this->client->delete_assigment_user_tasks($usertaskids);
            return $response->get_success();
        } catch (\Throwable) {
            return false;
        }
    }

}
