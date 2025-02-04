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
 * Privacy class for requesting user data.
 *
 * @package   assignsubmission_edulegit
 * @author    Alex Crosby <developer@edulegit.com>
 * @copyright @2024 EduLegit.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignsubmission_edulegit\privacy;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/assign/locallib.php');

use core_privacy\local\metadata\collection;
use core_privacy\local\request\writer;
use core_privacy\local\request\contextlist;
use mod_assign\privacy\assign_plugin_request_data;

/**
 * Privacy class for requesting user data.
 *
 * @package   assignsubmission_edulegit
 * @author    Alex Crosby <developer@edulegit.com>
 * @copyright @2024 EduLegit.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        \core_privacy\local\metadata\provider,
        \mod_assign\privacy\assignsubmission_provider,
        \mod_assign\privacy\assignsubmission_user_provider {

    /**
     * Return meta data about this plugin.
     *
     * @param collection $collection A list of information to add to.
     * @return collection Return the collection after adding to it.
     */
    public static function get_metadata(collection $collection): collection {
        $detail = [
                'assignment' => 'privacy:metadata:assignsubmission:assignment',
                'submission' => 'privacy:metadata:assignsubmission:submission',
                'title' => 'privacy:metadata:assignsubmission:title',
                'content' => 'privacy:metadata:assignsubmission:content',
                'documentid' => 'privacy:metadata:assignsubmission:documentid',
                'taskid' => 'privacy:metadata:assignsubmission:taskid',
                'taskuserid' => 'privacy:metadata:assignsubmission:taskuserid',
                'userid' => 'privacy:metadata:assignsubmission:userid',
                'userkey' => 'privacy:metadata:assignsubmission:userkey',
                'baseurl' => 'privacy:metadata:assignsubmission:baseurl',
                'url' => 'privacy:metadata:assignsubmission:url',
                'authkey' => 'privacy:metadata:assignsubmission:authkey',
                'score' => 'privacy:metadata:assignsubmission:score',
                'plagiarism' => 'privacy:metadata:assignsubmission:plagiarism',
                'airate' => 'privacy:metadata:assignsubmission:airate',
                'aiprobability' => 'privacy:metadata:assignsubmission:aiprobability',

        ];
        $collection->add_database_table('assignsubmission_edulegit', $detail, 'privacy:metadata:assignsubmission');

        $collection->add_external_location_link('edulegit_workspace', [
                'userid' => 'privacy:metadata:edulegit_workspace:userid',
                'useremail' => 'privacy:metadata:edulegit_workspace:useremail',
                'userfirstname' => 'privacy:metadata:edulegit_workspace:userfirstname',
                'userlastname' => 'privacy:metadata:edulegit_workspace:userlastname',
        ], 'privacy:metadata:edulegit_workspace');

        return $collection;
    }

    /**
     * This is covered by mod_assign provider and the query on assign_submissions.
     *
     * @param int $userid The user ID that we are finding contexts for.
     * @param contextlist $contextlist A context list to add sql and params to for contexts.
     */
    public static function get_context_for_userid_within_submission(int $userid, contextlist $contextlist) {
        // This is already fetched from mod_assign.
    }

    /**
     * This is also covered by the mod_assign provider and it's queries.
     *
     * @param \mod_assign\privacy\useridlist $useridlist An object for obtaining user IDs of students.
     */
    public static function get_student_user_ids(\mod_assign\privacy\useridlist $useridlist) {
        // No need.
    }

    /**
     * If you have tables that contain userids and you can generate entries in your tables without creating an
     * entry in the assign_submission table then please fill in this method.
     *
     * @param \core_privacy\local\request\userlist $userlist The userlist object
     */
    public static function get_userids_from_context(\core_privacy\local\request\userlist $userlist) {
        // Not required.
    }

    /**
     * Export all user data for this plugin.
     *
     * @param assign_plugin_request_data $exportdata Data used to determine which context and user to export and other useful
     * information to help with exporting.
     */
    public static function export_submission_user_data(assign_plugin_request_data $exportdata) {
        // We currently don't show submissions to teachers when exporting their data.
        if ($exportdata->get_user() != null) {
            return null;
        }
        // Retrieve text for this submission.
        $context = $exportdata->get_context();
        $assign = $exportdata->get_assign();
        $plugin = $assign->get_plugin_by_type('assignsubmission', 'edulegit');
        $submission = $exportdata->get_pluginobject();
        $editortext = $plugin->get_editor_text('edulegit', $submission->id);
        if (!empty($editortext)) {
            $submissiontext = new \stdClass();
            $currentpath = $exportdata->get_subcontext();
            $currentpath[] = get_string('privacy:metadata:assignsubmission', 'assignsubmission_edulegit');
            $submissiontext->text = $editortext;
            writer::with_context($context)
                    // Add the text to the exporter.
                    ->export_data($currentpath, $submissiontext);

            // Handle plagiarism data.
            $coursecontext = $context->get_course_context();
            $userid = $submission->userid;
            \core_plagiarism\privacy\provider::export_plagiarism_user_data($userid, $context, $currentpath, [
                    'cmid' => $context->instanceid,
                    'course' => $coursecontext->instanceid,
                    'userid' => $userid,
                    'content' => $editortext,
                    'assignment' => $submission->assignment,
            ]);
        }
    }

    /**
     * Any call to this method should delete all user data for the context defined in the deletion_criteria.
     *
     * @param assign_plugin_request_data $requestdata Data useful for deleting user data from this sub-plugin.
     */
    public static function delete_submission_for_context(assign_plugin_request_data $requestdata) {
        global $DB;

        \core_plagiarism\privacy\provider::delete_plagiarism_for_context($requestdata->get_context());

        try {
            $plugin = $requestdata->get_assign()->get_plugin_by_type('assignsubmission', 'edulegit');
            /* @var $submissionmanager \assignsubmission_edulegit\edulegit_submission_manager
             * Define the variable type for better IDE autocomplete and code readability.
             */
            $submissionmanager = $plugin->get_edulegit()->get_manager();
            $submissionmanager->delete_assignment($requestdata->get_assignid());

        } catch (\Throwable $e) {
            // Delete the records in the table.
            $DB->delete_records('assignsubmission_edulegit', ['assignment' => $requestdata->get_assignid()]);
        }

    }

    /**
     * A call to this method should delete user data (where practicle) from the userid and context.
     *
     * @param assign_plugin_request_data $exportdata Details about the user and context to focus the deletion.
     */
    public static function delete_submission_for_userid(assign_plugin_request_data $exportdata) {
        global $DB;

        \core_plagiarism\privacy\provider::delete_plagiarism_for_user($exportdata->get_user()->id, $exportdata->get_context());

        $submissionid = $exportdata->get_pluginobject()->id;
        $assignmentid = $exportdata->get_assignid();
        try {
            $plugin = $exportdata->get_assign()->get_plugin_by_type('assignsubmission', 'edulegit');
            /* @var $submissionmanager \assignsubmission_edulegit\edulegit_submission_manager
             * Define the variable type for better IDE autocomplete and code readability.
             */
            $submissionmanager = $plugin->get_edulegit()->get_manager();
            $submissionmanager->delete_assignment($assignmentid, $submissionid);
        } catch (\Throwable $e) {
            // Delete the records in the table.
            $DB->delete_records('assignsubmission_edulegit', ['assignment' => $assignmentid,
                    'submission' => $submissionid]);
        }
    }

    /**
     * Deletes all submissions for the submission ids / userids provided in a context.
     * assign_plugin_request_data contains:
     * - context
     * - assign object
     * - submission ids (pluginids)
     * - user ids
     *
     * @param assign_plugin_request_data $deletedata A class that contains the relevant information required for deletion.
     */
    public static function delete_submissions(assign_plugin_request_data $deletedata) {
        global $DB;

        \core_plagiarism\privacy\provider::delete_plagiarism_for_users($deletedata->get_userids(), $deletedata->get_context());

        $submissionids = $deletedata->get_submissionids();

        if (empty($submissionids)) {
            return;
        }

        $assignmentid = $deletedata->get_assignid();

        try {
            $plugin = $deletedata->get_assign()->get_plugin_by_type('assignsubmission', 'edulegit');
            /* @var $submissionmanager \assignsubmission_edulegit\edulegit_submission_manager
             * Define the variable type for better IDE autocomplete and code readability.
             */
            $submissionmanager = $plugin->get_edulegit()->get_manager();
        } catch (\Throwable $e) {
            $submissionmanager = null;
        }

        foreach ($submissionids as $submissionid) {
            try {
                if ($submissionmanager) {
                    $submissionmanager->delete_assignment($assignmentid, $submissionid);
                } else {
                    $DB->delete_records('assignsubmission_edulegit', ['assignment' => $assignmentid,
                            'submission' => $submissionid]);
                }
            } catch (\Throwable $e) {
                // Delete the records in the table.
                $DB->delete_records('assignsubmission_edulegit', ['assignment' => $assignmentid,
                        'submission' => $submissionid]);
            }
        }

    }
}
