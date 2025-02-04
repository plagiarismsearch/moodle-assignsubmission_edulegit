<?php

namespace assignsubmission_edulegit\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;
use core_external\external_single_structure;

defined('MOODLE_INTERNAL') || die();

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
        ]);
    }

    /**
     * Handle the EduLegit webhook payload.
     *
     * @param string $event Event name.
     * @param string $data The JSON payload.
     * @return array The result of the webhook processing.
     * @throws \invalid_parameter_exception
     */
    public static function execute(string $event, string $data): array {

        $params = self::validate_parameters(self::execute_parameters(), ['event' => $event, 'data' => $data]);

        $decodedData = \assignsubmission_edulegit\edulegit_helper::json_decode($params['data']);

        if ($decodedData === null) {
            throw new \invalid_parameter_exception('Invalid data payload.');
        }

        $callback = new \assignsubmission_edulegit\edulegit_callback();
        $result = $callback->handle($params['event'] ?? '', $decodedData);

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
