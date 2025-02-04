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
 * Strings for component 'assignsubmission_edulegit', language 'en'
 *
 * @package   assignsubmission_edulegit
 * @author    Alex Crosby <developer@edulegit.com>
 * @copyright @2024 EduLegit.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// phpcs:disable moodle.Files.LangFilesOrdering.IncorrectOrder

$string['edulegit'] = 'EduLegit workspace';
$string['pluginname'] = 'EduLegit workspace';
$string['pluginabout'] =
        '<a href="https://edulegit.com/">EduLegit.com</a> is AI management control system, teacher assistant and student supervision';
$string['default'] = 'Selected by default in new assignments';
$string['default_help'] = 'If set, this submission method will be selected by default for all new assignments.';
$string['api_token'] = 'EduLegit API token';
$string['api_token_help'] = 'Your API token can be found in the EduLegit admin panel.';

$string['ws_token'] = 'Web service token';
$string['ws_token_help'] =
        'This token is used to authenticate external web service requests for the plugin. You can generate it by navigating to <em>Site administration > Server > Web services > Manage tokens > Add</em>.';

$string['enabled'] = 'EduLegit submission';
$string['enabled_help'] = 'If enabled, students can edit their document submissions on the EduLegit AI management control system.';

$string['enable_screen'] = 'Enable Screen recording';
$string['enable_screen_label'] = 'EduLegit screen settings';
$string['enable_screen_help'] = 'Enable the option to record a video from a user’s camera during the task completion';

$string['enable_camera'] = 'Enable Camera recording';
$string['enable_camera_label'] = 'EduLegit camera settings';
$string['enable_camera_help'] = 'Enable the option to record a user’s screen during the task completion';

$string['enable_attention'] = 'Enable Attention map recognition';
$string['enable_attention_label'] = 'EduLegit attention settings';
$string['enable_attention_help'] = 'Enable the option to recognize attention map from a user’s camera during the task completion';

$string['enable_plagiarism'] = 'Enable Auto plagiarism check';
$string['enable_plagiarism_label'] = 'EduLegit plagiarism checker';
$string['enable_plagiarism_help'] = 'Enable plagiarism check option for every submitted task';

$string['enable_ai'] = 'Enable Auto AI check';
$string['enable_ai_label'] = 'EduLegit AI checker';
$string['enable_ai_help'] = 'Enable AI check option for every submitted task';

$string['as_view'] = 'View on EduLegit';
$string['as_pdf'] = 'Download PDF';
$string['as_docx'] = 'Download Docx';
$string['as_html'] = 'Download Html';
$string['as_txt'] = 'Download plain text';
$string['default_filename'] = 'edulegit.html';
$string['submission'] = 'Submission';

$string['open_edulegit'] = 'Open EduLegit workspace';
$string['open_edulegit_label'] = 'EduLegit submission';
$string['open_edulegit_help'] = 'Open EduLegit workspace to manage your submission, track your progress effectively.';
$string['open_edulegit_error'] = 'EduLegit service error: ';
$string['default_open_edulegit_error'] = 'An error occurred while fetching data from EduLegit. Please try again later.';

$string['eventassessableuploaded'] = 'EduLegit submission content uploaded.';

$string['privacy:metadata:assignsubmission'] = 'Stores submission-related data for assignments in EduLegit Workspace.';

$string['privacy:metadata:assignsubmission:assignment'] = 'The unique identifier of the assignment associated with the submission.';
$string['privacy:metadata:assignsubmission:submission'] = 'The unique identifier of the user’s submission.';
$string['privacy:metadata:assignsubmission:title'] = 'The title of the EduLegit submission.';
$string['privacy:metadata:assignsubmission:content'] = 'The content of the EduLegit submission.';
$string['privacy:metadata:assignsubmission:documentid'] = 'The unique identifier of the document created on EduLegit.';
$string['privacy:metadata:assignsubmission:taskid'] = 'The unique identifier of the task related to the EduLegit submission.';
$string['privacy:metadata:assignsubmission:taskuserid'] =
        'The unique identifier of the user associated with the EduLegit submission task.';
$string['privacy:metadata:assignsubmission:userid'] = 'The unique identifier of the user who submitted to EduLegit.';
$string['privacy:metadata:assignsubmission:userkey'] = 'A unique key identifying the user in EduLegit.';
$string['privacy:metadata:assignsubmission:baseurl'] = 'The base URL for accessing the EduLegit submission.';
$string['privacy:metadata:assignsubmission:url'] = 'The shareable URL of the EduLegit submission document.';
$string['privacy:metadata:assignsubmission:authkey'] = 'An authentication key for secure access to the EduLegit submission.';
$string['privacy:metadata:assignsubmission:score'] = 'The EduLegit score assigned to the submission.';
$string['privacy:metadata:assignsubmission:plagiarism'] = 'The plagiarism score determined for the submission.';
$string['privacy:metadata:assignsubmission:airate'] = 'The AI-generated rating assigned to the submission.';
$string['privacy:metadata:assignsubmission:aiprobability'] =
        'The probability score indicating whether the submission was AI-generated.';

$string['privacy:metadata:edulegit_workspace'] =
        'EduLegit Workspace requires user data to generate and display the EduLegit submission.';
$string['privacy:metadata:edulegit_workspace:userid'] =
        'The userid is transmitted to EduLegit Workspace to grant access to user data within the platform.';
$string['privacy:metadata:edulegit_workspace:useremail'] =
        'The user email is sent to EduLegit Workspace to identify users and facilitate authentication.';
$string['privacy:metadata:edulegit_workspace:userfirstname'] =
        'The user\'s first name is sent to the EduLegit Workspace to ensure full identification for the user\'s teacher.';
$string['privacy:metadata:edulegit_workspace:userlastname'] =
        'The user\'s last name is sent to the EduLegit Workspace to ensure full identification for the user\'s teacher.';

// phpcs:enable moodle.Files.LangFilesOrdering.IncorrectOrder
