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
 * This file defines the admin settings for this plugin.
 *
 * @package   assignsubmission_edulegit
 * @author    Alex Crosby <developer@edulegit.com>
 * @copyright @2024 EduLegit.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$settings->add(new admin_setting_configtext('assignsubmission_edulegit/ws_token',
        new lang_string('ws_token', 'assignsubmission_edulegit'),
        new lang_string('ws_token_help', 'assignsubmission_edulegit'),
        '',
        PARAM_RAW, 30));

$settings->add(new admin_setting_configtext('assignsubmission_edulegit/api_token',
        new lang_string('api_token', 'assignsubmission_edulegit'),
        new lang_string('api_token_help', 'assignsubmission_edulegit'),
        '',
        PARAM_RAW, 30));

$settings->add(new admin_setting_configcheckbox('assignsubmission_edulegit/default',
        new lang_string('default', 'assignsubmission_edulegit'),
        new lang_string('default_help', 'assignsubmission_edulegit'), 0));

$settings->add(new admin_setting_configcheckbox('assignsubmission_edulegit/enable_attention',
        new lang_string('enable_attention', 'assignsubmission_edulegit'),
        new lang_string('enable_attention_help', 'assignsubmission_edulegit'), 0));

$settings->add(new admin_setting_configcheckbox('assignsubmission_edulegit/enable_camera',
        new lang_string('enable_camera', 'assignsubmission_edulegit'),
        new lang_string('enable_camera_help', 'assignsubmission_edulegit'), 0));

$settings->add(new admin_setting_configcheckbox('assignsubmission_edulegit/enable_screen',
        new lang_string('enable_screen', 'assignsubmission_edulegit'),
        new lang_string('enable_screen_help', 'assignsubmission_edulegit'), 0));

$settings->add(new admin_setting_configcheckbox('assignsubmission_edulegit/enable_plagiarism',
        new lang_string('enable_plagiarism', 'assignsubmission_edulegit'),
        new lang_string('enable_plagiarism_help', 'assignsubmission_edulegit'), 0));

$settings->add(new admin_setting_configcheckbox('assignsubmission_edulegit/enable_ai',
        new lang_string('enable_ai', 'assignsubmission_edulegit'),
        new lang_string('enable_ai_help', 'assignsubmission_edulegit'), 0));
