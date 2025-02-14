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

namespace assignsubmission_edulegit\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;
use core_external\external_single_structure;

/**
 * Webhook handler for the EduLegit assignment submission plugin.
 *
 * @package   assignsubmission_edulegit
 * @author    Alex Crosby <developer@edulegit.com>
 * @copyright @2025 EduLegit.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class webhook_handler extends external_api {
    /**
     * Define the parameters for the webhook handler.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
                'event' => new external_value(PARAM_STRINGID, 'Event name sent by EduLegit'),
                'data' => new external_value(PARAM_RAW, 'JSON data payload sent by EduLegit'),
                'cmid' => new external_value(PARAM_INT, 'Course module ID'),
        ]);
    }

    /**
     * Handle the EduLegit webhook payload.
     *
     * @param string $event Event name.
     * @param string $data The JSON payload.
     * @param int $cmid Course module ID for context validation.
     * @return array The result of the webhook processing.
     * @throws \invalid_parameter_exception
     * @throws \required_capability_exception
     */
    public static function execute(string $event, string $data, int $cmid): array {

        $params = self::validate_parameters(self::execute_parameters(), ['event' => $event, 'data' => $data, 'cmid' => $cmid]);

        $context = \context_module::instance($params['cmid']);
        self::validate_context($context);
        require_capability('mod/assign:submit', $context);

        $dataobject = \assignsubmission_edulegit\edulegit_helper::json_decode($params['data']);
        if ($dataobject === null) {
            throw new \invalid_parameter_exception('Invalid data payload.');
        }

        $callback = new \assignsubmission_edulegit\edulegit_callback();
        $result = $callback->handle($params['event'] ?? '', $dataobject);

        return [
                'success' => true,
                'data' => $result,
        ];
    }

    /**
     * Define the return structure for the webhook handler.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
                'success' => new external_value(PARAM_BOOL, 'Success flag'),
                'data' => new external_value(PARAM_RAW, 'Result data from the callback'),
        ]);
    }
}
