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
 * This file contains the definition for the library class for edulegit submission plugin.
 *
 *
 * @package   assignsubmission_edulegit
 * @author    Alex Crosby <developer@edulegit.com>
 * @copyright @2024 EduLegit.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use assignsubmission_edulegit\edulegit_submission_entity;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/assign/submission/edulegit/lib.php');

/**
 * Library class for edulegit submission plugin extending submission plugin base class.
 *
 * @package   assignsubmission_edulegit
 * @author    Alex Crosby <developer@edulegit.com>
 * @copyright @2024 EduLegit.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_submission_edulegit extends assign_submission_plugin {
    /**
     * Get the name of the edulegit submission plugin.
     *
     * @return string
     */
    public function get_name(): string {
        return $this->translate('edulegit');
    }

    /**
     * Get the default setting for EduLegit submission plugin
     *
     * @param MoodleQuickForm $mform The form to add elements to
     * @return void
     */
    public function get_settings(MoodleQuickForm $mform) {

        $assignmenthasintance = $this->assignment->has_instance();
        $config = $assignmenthasintance ? $this->get_edulegit()->get_config() : null;

        $name = $this->translate('enable_attention_label');
        $mform->addElement('advcheckbox', 'assignsubmission_edulegit_enable_attention',
                $name, $this->translate('enable_attention'), null, [0, 1]);

        if ($config) {
            $mform->setDefault('assignsubmission_edulegit_enable_attention',
                    $config->get_plugin_or_global_config('enable_attention'));
        }
        $mform->addHelpButton('assignsubmission_edulegit_enable_attention',
                'enable_attention',
                'assignsubmission_edulegit');
        $mform->hideIf('assignsubmission_edulegit_enable_attention', 'assignsubmission_edulegit_enabled', 'notchecked');

        $name = $this->translate('enable_camera_label');
        $mform->addElement('advcheckbox', 'assignsubmission_edulegit_enable_camera',
                $name, $this->translate('enable_camera'), null, [0, 1]);
        if ($config) {
            $mform->setDefault('assignsubmission_edulegit_enable_camera',
                    $config->get_plugin_or_global_config('enable_camera'));
        }
        $mform->addHelpButton('assignsubmission_edulegit_enable_camera',
                'enable_camera',
                'assignsubmission_edulegit');
        $mform->hideIf('assignsubmission_edulegit_enable_camera', 'assignsubmission_edulegit_enabled', 'notchecked');

        $name = $this->translate('enable_screen_label');
        $mform->addElement('advcheckbox', 'assignsubmission_edulegit_enable_screen',
                $name, $this->translate('enable_screen'), null, [0, 1]);
        if ($config) {
            $mform->setDefault('assignsubmission_edulegit_enable_screen',
                    $config->get_plugin_or_global_config('enable_screen'));
        }
        $mform->addHelpButton('assignsubmission_edulegit_enable_screen',
                'enable_screen',
                'assignsubmission_edulegit');
        $mform->hideIf('assignsubmission_edulegit_enable_screen', 'assignsubmission_edulegit_enabled', 'notchecked');

        $name = $this->translate('enable_plagiarism_label');
        $mform->addElement('advcheckbox', 'assignsubmission_edulegit_enable_plagiarism',
                $name, $this->translate('enable_plagiarism'), null, [0, 1]);
        if ($config) {
            $mform->setDefault('assignsubmission_edulegit_enable_plagiarism',
                    $config->get_plugin_or_global_config('enable_plagiarism'));
        }
        $mform->addHelpButton('assignsubmission_edulegit_enable_plagiarism',
                'enable_plagiarism',
                'assignsubmission_edulegit');
        $mform->hideIf('assignsubmission_edulegit_enable_plagiarism', 'assignsubmission_edulegit_enabled', 'notchecked');

        $name = $this->translate('enable_ai_label');
        $mform->addElement('advcheckbox', 'assignsubmission_edulegit_enable_ai',
                $name, $this->translate('enable_ai'), null, [0, 1]);
        if ($config) {
            $mform->setDefault('assignsubmission_edulegit_enable_ai',
                    $config->get_plugin_or_global_config('enable_ai'));
        }
        $mform->addHelpButton('assignsubmission_edulegit_enable_ai',
                'enable_ai',
                'assignsubmission_edulegit');
        $mform->hideIf('assignsubmission_edulegit_enable_ai', 'assignsubmission_edulegit_enabled', 'notchecked');

    }

    /**
     * Save the settings for EduLegit submission plugin
     *
     * @param stdClass $formdata
     * @return bool
     */
    public function save_settings(stdClass $formdata) {

        $this->set_config('enable_attention', $formdata->assignsubmission_edulegit_enable_attention);
        $this->set_config('enable_camera', $formdata->assignsubmission_edulegit_enable_camera);
        $this->set_config('enable_screen', $formdata->assignsubmission_edulegit_enable_screen);
        $this->set_config('enable_plagiarism', $formdata->assignsubmission_edulegit_enable_plagiarism);
        $this->set_config('enable_ai', $formdata->assignsubmission_edulegit_enable_ai);

        return true;

    }

    /**
     * Add form elements for settings.
     *
     * @param mixed $submissionorgrade can be null
     * @param MoodleQuickForm $mform
     * @param stdClass $data
     * @return bool if elements were added to the form
     */
    public function get_form_elements($submissionorgrade, MoodleQuickForm $mform, stdClass $data) {
        global $USER;
        $submissionmanager = $this->get_edulegit()->get_manager();
        $edulegitsubmission = $submissionmanager->init($submissionorgrade, ['user' => $USER]);
        if (!$edulegitsubmission) {
            return false;
        }

        $redirecturl = $edulegitsubmission->get_user_login_url();
        if (!$redirecturl || $edulegitsubmission->error) {
            $error = $this->translate('open_edulegit_error') . ' ' . $edulegitsubmission->error ?:
                    $this->translate('default_open_edulegit_error');

            global $OUTPUT;
            $html = $OUTPUT->notification($error, 'error');
            $mform->addElement('html', $html, $this->get_name(), null, null);

            return false;
        }

        $mform->addElement('hidden', 'edulegit_submission_id', $edulegitsubmission->id);
        $mform->setType('edulegit_submission_id', PARAM_INT);

        $label = $this->translate('open_edulegit_label');

        $button = html_writer::link($redirecturl, $this->translate('open_edulegit'), [
                'type' => 'button',
                'class' => 'btn btn-primary',
                'target' => '_blank',
                'title' => $this->translate('open_edulegit_help'),
        ]);

        $html = '<div class="mb-3 row">
            <div class="col-md-3 d-flex col-form-label pb-0 pr-md-0">' . $label . '</div>
            <div class="col-md-9 d-flex flex-wrap align-items-start">' . $button . '</div>
        </div>';

        $mform->addElement('html', $html, $this->get_name(), null, null);

        return true;
    }

    /**
     * Save data to the database and trigger plagiarism plugin,
     * if enabled, to scan the uploaded content via events trigger.
     *
     * @param stdClass $submissionorgrade
     * @param stdClass $data
     * @return bool
     */
    public function save(stdClass $submissionorgrade, stdClass $data): bool {
        global $DB, $USER;

        $edulegitsubmission = $this->get_edulegit()->get_manager()->sync($submissionorgrade->id);

        if (!$edulegitsubmission) {
            $edulegitsubmission = $this->get_edulegit_submission($submissionorgrade->id);
        }

        $params = [
                'context' => context_module::instance($this->assignment->get_course_module()->id),
                'courseid' => $this->assignment->get_course()->id,
                'objectid' => $submissionorgrade->id,
                'other' => [
                        'content' => trim($edulegitsubmission->content),
                        'pathnamehashes' => [],
                ],
        ];

        if (!empty($submissionorgrade->userid) && ($submissionorgrade->userid != $USER->id)) {
            $params['relateduserid'] = $submissionorgrade->userid;
        }
        if ($this->assignment->is_blind_marking()) {
            $params['anonymous'] = 1;
        }

        // Trigger assessable_uploaded event.
        $event = \assignsubmission_edulegit\event\assessable_uploaded::create($params);
        $event->trigger();

        // Get the group name as other fields are not transcribed in the logs and this information is important.
        $groupname = null;
        $groupid = 0;
        if (empty($submissionorgrade->userid) && !empty($submissionorgrade->groupid)) {
            $groupname = $DB->get_field('groups', 'name', ['id' => $submissionorgrade->groupid], MUST_EXIST);
            $groupid = $submissionorgrade->groupid;
        } else {
            $params['relateduserid'] = $submissionorgrade->userid;
        }

        // Adapt $params to be used for the submisssion_xxxxx events.
        unset($params['objectid']);
        unset($params['other']);
        $params['other'] = [
                'submissionid' => $submissionorgrade->id,
                'submissionattempt' => $submissionorgrade->attemptnumber,
                'submissionstatus' => $submissionorgrade->status,
                'groupid' => $groupid,
                'groupname' => $groupname,
        ];

        // Trigger submission created event.
        $params['objectid'] = $edulegitsubmission->id;
        $event = \assignsubmission_edulegit\event\submission_created::create($params);
        $event->set_assign($this->assignment);
        $event->trigger();

        return $edulegitsubmission->id && !$edulegitsubmission->error;
    }

    /**
     * Remove a submission.
     *
     * @param stdClass $submission The submission.
     * @return boolean
     */
    public function remove(stdClass $submission): bool {
        return $this->get_edulegit()->get_manager()->delete_submission($submission ? $submission->id : 0);
    }

    /**
     * Display EduLegit result in the submission status table.
     *
     * @param stdClass $submissionorgrade
     * @param bool $showviewlink - If the summary has been truncated set this to true
     * @return string
     */
    public function view_summary(stdClass $submissionorgrade, &$showviewlink): string {
        $edulegitsubmission = $this->get_edulegit_submission($submissionorgrade->id);
        if ($edulegitsubmission && $edulegitsubmission->url) {

            global $PAGE;
            /** @var core_renderer $output */
            $output = $PAGE->get_renderer('core');

            $menubuilder = new \assignsubmission_edulegit\edulegit_submission_menu_builder();
            $menu = $menubuilder->build($edulegitsubmission);

            $html = $output->render($menu);
        }
        return $html;
    }

    /**
     * Produce a list of files suitable for export that represent this submission.
     *
     * @param stdClass $submissionorgrade - For this is the submission data
     * @param stdClass $user - This is the user record for this submission
     * @return array - return an array of files indexed by filename
     */
    public function get_files(stdClass $submissionorgrade, stdClass $user): array {
        $files = [];

        $edulegitsubmission = $this->get_edulegit_submission($submissionorgrade->id);
        if ($edulegitsubmission) {
            $filename = $this->translate('default_filename');
            $files[$filename] = [$edulegitsubmission->content];
        }

        return $files;
    }

    /**
     * The assignment has been deleted - remove the plugin specific data.
     *
     * @return bool
     */
    public function delete_instance(): bool {
        $assignmentid = $this->assignment->get_instance()->id;
        return $this->get_edulegit()->get_manager()->delete_assignment($assignmentid);
    }

    /**
     * Is this assignment plugin empty?
     *
     * @param stdClass $submissionorgrade
     * @return bool
     */
    public function is_empty(stdClass $submissionorgrade): bool {
        $edulegitsubmission = $this->get_edulegit_submission($submissionorgrade->id);

        return !$edulegitsubmission || $edulegitsubmission->is_empty();
    }

    /**
     * Determine if a submission is empty.
     *
     * This is distinct from is_empty in that it is intended to be used to
     * determine if a submission made before saving is empty.
     *
     * @param stdClass $data The submission data
     * @return bool
     */
    public function submission_is_empty(stdClass $data): bool {
        return empty($data->edulegit_submission_id);
    }

    /**
     * Get file areas returns a list of areas this plugin stores files.
     *
     * @return array - An array of fileareas (keys) and descriptions (values)
     */
    public function get_file_areas(): array {
        // For now, this plugin doesn't store any file.
        return [];
    }

    /**
     * Copy the student's submission from a previous submission.
     * Used when a student opts to base their resubmission on the last submission.
     *
     * @param stdClass $oldsubmission
     * @param stdClass $submission
     * @return bool
     */
    public function copy_submission(stdClass $oldsubmission, stdClass $submission): bool {
        // Copy the assignsubmission_edulegit record.
        $edulegitsubmission = $this->get_edulegit_submission($oldsubmission->id);
        if ($edulegitsubmission) {

            $newsubmission = new edulegit_submission_entity();
            $newsubmission->submission = $submission->id;
            $newsubmission->assignment = $edulegitsubmission->assignment;
            $newsubmission->content = $edulegitsubmission->content;
            $newsubmission->status = 0;
            $newsubmission->error = '';

            return $this->get_edulegit()->get_repository()->insert_submission($newsubmission);
        }
        return true;
    }

    /**
     * Return a list of the text fields that can be imported/exported by this plugin.
     *
     * @return array An array of field names and descriptions. (name=>description, ...)
     */
    public function get_editor_fields(): array {
        return ['edulegit' => $this->translate('pluginname')];
    }

    /**
     * Get the saved text content from the editor.
     *
     * @param string $name
     * @param int $submissionorgradeid
     * @return string
     */
    public function get_editor_text($name, $submissionorgradeid): string {
        if ($name == 'edulegit') {
            $edulegitsubmission = $this->get_edulegit_submission($submissionorgradeid);
            if ($edulegitsubmission) {
                return $edulegitsubmission->content;
            }
        }

        return '';
    }

    /**
     * Get the content format for the editor.
     *
     * @param string $name
     * @param int $submissionid
     * @return int
     */
    public function get_editor_format($name, $submissionid): int {
        if ($name == 'edulegit') {
            $edulegitsubmission = $this->get_edulegit_submission($submissionid);
            if ($edulegitsubmission) {
                return FORMAT_HTML;
            }
        }

        return 0;
    }

    /**
     * Get submission information from the database.
     *
     * @param int $submissionid
     * @return mixed
     */
    private function get_edulegit_submission(int $submissionid): ?edulegit_submission_entity {
        return $this->get_edulegit()->get_repository()->get_submission($submissionid);
    }

    /**
     * Edulegit core instance.
     *
     * @var \assignsubmission_edulegit\edulegit_core|null
     */
    private ?\assignsubmission_edulegit\edulegit_core $edulegit = null;

    /**
     * Lazy load the edulegit core instance.
     *
     * @return \assignsubmission_edulegit\edulegit_core
     */
    public function get_edulegit(): \assignsubmission_edulegit\edulegit_core {
        if ($this->edulegit === null) {
            $this->edulegit = new \assignsubmission_edulegit\edulegit_core($this->get_config());
        }
        return $this->edulegit;
    }

    /**
     * Returns a localized string.
     *
     * @param string $identifier The key identifier for the localized string
     * @return lang_string|string
     */
    private function translate(string $identifier) {
        return get_string($identifier, 'assignsubmission_edulegit');
    }

}
