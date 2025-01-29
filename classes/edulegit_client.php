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
 * The assignsubmission_edulegit API client class.
 *
 * Handles communication with the EduLegit API for Moodle assignments.
 *
 * @package   assignsubmission_edulegit
 * @author    Alex Crosby <developer@edulegit.com>
 * @copyright @2024 EduLegit.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignsubmission_edulegit;

/**
 * Class edulegit_client
 *
 * This class is responsible for sending requests to the EduLegit API and managing the API connection.
 */
class edulegit_client {

    /**
     * API authentication key.
     *
     * @var string
     */
    private string $authkey = '';

    /**
     * Base URL for the EduLegit API.
     *
     * @var string
     */
    private string $baseurl = 'https://api.edulegit.com';

    /**
     * Enables or disables debugging.
     *
     * @var bool
     */
    private bool $debug = true;

    /**
     * Constructor for the edulegit_client class.
     *
     * @param string $authkey The authentication key for API access.
     */
    public function __construct(string $authkey) {
        $this->authkey = $authkey;
    }

    /**
     * Sends a request to the EduLegit API.
     *
     * @param string $method The HTTP method to use (e.g., 'POST', 'GET').
     * @param string $uri The URI of the API endpoint.
     * @param array $data The data to be sent with the request.
     * @return edulegit_client_response The API response.
     */
    public function fetch(string $method, string $uri, array $data = []): edulegit_client_response {
        $url = $this->baseurl . $uri;

        $headers = [
                'X-API-TOKEN' => $this->authkey,
                'Content-Type' => 'application/json',
                'User-Agent' => 'Mozilla/5.0 Edulegit plugin/1.0',
        ];

        $options = [
                'CURLOPT_TIMEOUT' => 10,
                'CURLOPT_CONNECTTIMEOUT' => 7,
        ];

        if ($this->debug) {
            $options = array_merge($options, [
                    'CURLOPT_SSL_VERIFYPEER' => false,
                    'CURLOPT_SSL_VERIFYHOST' => false,
            ]);
        }

        $curl = new \curl();
        $curl->setHeader($headers);

        if ($method === 'POST') {
            $jsondata = edulegit_helper::json_encode($data);
            $body = $curl->post($url, $jsondata, $options);
        } else {
            $body = $curl->get($url, $data, $options);
        }

        return new edulegit_client_response((string) $body, (array) $curl->get_info(), (string) $curl->error, $url);
    }

    /**
     * Initializes a Moodle assignment via the API.
     *
     * @param array $data The data to initialize the assignment.
     * @return edulegit_client_response The API response.
     */
    public function init_assignment($data): edulegit_client_response {
        return $this->fetch('POST', '/init-moodle-assignment', $data);
    }
}
