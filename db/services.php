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
 * Services configuration
 *
 * @package   assignsubmission_edulegit
 * @author    Alex Crosby <developer@edulegit.com>
 * @copyright @2024 EduLegit.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
        'assignsubmission_edulegit_webhook_handler' => [
                'classname' => 'assignsubmission_edulegit\external\webhook_handler',
                'methodname' => 'execute',
                'classpath' => '',
                'description' => 'Handles EduLegit webhook requests.',
                'type' => 'write',
                'ajax' => true,
        ],
];

$services = [
        'EduLegit Webhook Service' => [
                'functions' => ['assignsubmission_edulegit_webhook_handler'],
                'restrictedusers' => 0,
                'enabled' => 1,
        ],
];

