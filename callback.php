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
 * Handles Edulegit webhooks
 *
 * @package   assignsubmission_edulegit
 * @author    Alex Crosby <developer@edulegit.com>
 * @copyright @2024 EduLegit.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define('NO_MOODLE_COOKIES', true);

require_once(dirname(__DIR__) . '/../../../config.php');

try {
    $rawbody = file_get_contents('php://input');
    if (!$rawbody) {
        header('HTTP/1.1 400 Bad Request');
        die;
    }

    $token = get_config('assignsubmission_edulegit', 'api_token');
    $validator = new \assignsubmission_edulegit\edulegit_payload_validator($token);

    $payload = \assignsubmission_edulegit\edulegit_helper::json_decode($rawbody);

    if (!$validator->is_valid($payload)) {
        header('HTTP/1.1 400 Bad Request');
        die;
    }

    if (!$validator->is_signed($payload)) {
        header('HTTP/1.1 403 Invalid signature');
        die;
    }

    $callback = new \assignsubmission_edulegit\edulegit_callback();
    $result = $callback->handle($payload->event ?? '', $payload->data ?? []);

    echo \assignsubmission_edulegit\edulegit_helper::json_encode($result);
} catch (\Throwable $exception) {
    header('HTTP/1.1 400 Bad Request');
    throw $exception;
}
