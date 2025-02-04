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
 * The assignsubmission_edulegit core class.
 *
 * @package   assignsubmission_edulegit
 * @author    Alex Crosby <developer@edulegit.com>
 * @copyright @2024 EduLegit.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignsubmission_edulegit;

/**
 * Callback handler for the edulegit plugin.
 *
 * This class handles incoming payloads and processes specific events
 * related to the EduLegit webhook request.
 *
 * @package   assignsubmission_edulegit
 * @author    Alex Crosby <developer@edulegit.com>
 * @copyright @2024 EduLegit.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edulegit_callback {

    /**
     * Instance of the edulegit submission repository.
     *
     * @var edulegit_submission_repository
     */
    protected edulegit_submission_repository $repository;

    /**
     * Constructor for edulegit_callback class.
     *
     * @param edulegit_submission_repository|null $repository An instance of edulegit_submission_repository or null.
     */
    public function __construct(?edulegit_submission_repository $repository = null) {
        $this->repository = $repository ?? new edulegit_submission_repository();
    }

    /**
     * Handles incoming payload and processes the event.
     *
     * @param string $event Event name of action.
     * @param object $data The payload object containing the event and data.
     * @return mixed Returns the result of the event handling or null if the event is not recognized.
     */
    public function handle(string $event, object $data) {
        if ($event === 'taskUser.sync') {
            return $this->sync_task_user($data);
        }
        return null;
    }

    /**
     * Synchronizes task user information with the repository.
     *
     * @param object $data The data object containing the task user's information.
     * @return int|null Returns the submission ID if successful, or null if not.
     */
    private function sync_task_user($data) {
        $id = $data->externalId ?? null;
        if (!$id) {
            return null;
        }

        $submission = $this->repository->get_by_id($id);

        if (!$submission) {
            return null;
        }

        $submission->status = 1;
        $submission->error = '';

        if (isset($data->title)) {
            $submission->title = $data->title;
        }
        if (isset($data->content)) {
            $submission->content = $data->content;
        }
        if (isset($data->url)) {
            $submission->url = $data->url;
        }
        if (isset($data->authKey)) {
            $submission->authkey = $data->authKey;
        }
        if (isset($data->score)) {
            $submission->score = $data->score;
        }
        if (isset($data->plagiarism)) {
            $submission->plagiarism = $data->plagiarism;
        }
        if (isset($data->aiAverageProbability)) {
            $submission->airate = $data->aiAverageProbability;
        }
        if (isset($data->aiProbability)) {
            $submission->aiprobability = $data->aiProbability;
        }
        if (isset($data->loginTimeToken)) {
            $submission->userkey = $data->loginTimeToken;
        }

        return $this->repository->update_submission($submission) ? $submission->id : null;
    }
}
