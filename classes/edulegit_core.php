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
 * This class serves as the core handler for managing configuration, repository, and submission manager interactions
 * within the assignsubmission_edulegit plugin.
 *
 * @package   assignsubmission_edulegit
 * @author    Alex Crosby <developer@edulegit.com>
 * @copyright @2024 EduLegit.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignsubmission_edulegit;

/**
 * Class edulegit_core
 *
 * Core class to manage the primary plugin components including configuration, repository, and submission management.
 */
class edulegit_core {

    /**
     * Holds the configuration for the plugin.
     *
     * @var \assignsubmission_edulegit\edulegit_config
     */
    private \assignsubmission_edulegit\edulegit_config $config;

    /**
     * Repository to manage submission records.
     *
     * @var \assignsubmission_edulegit\edulegit_submission_repository
     */
    private \assignsubmission_edulegit\edulegit_submission_repository $repository;

    /**
     * Manager to handle submission-related operations.
     *
     * @var \assignsubmission_edulegit\edulegit_submission_manager
     */
    private \assignsubmission_edulegit\edulegit_submission_manager $manager;

    /**
     * Initializes the core components: config, repository, and submission manager.
     *
     * @param \stdClass $config Configuration object containing plugin settings.
     */
    public function __construct(\stdClass $config) {
        $this->config = new \assignsubmission_edulegit\edulegit_config($config);
        $this->repository = new \assignsubmission_edulegit\edulegit_submission_repository();
        $client = new edulegit_client($this->config->get_global('api_token') ?? '');
        $this->manager = new \assignsubmission_edulegit\edulegit_submission_manager($this->config, $client, $this->repository);
    }

    /**
     * Get the plugin configuration instance.
     *
     * Provides access to the configuration settings specific to this plugin.
     *
     * @return edulegit_config The plugin configuration object.
     */
    public function get_config(): edulegit_config {
        return $this->config;
    }

    /**
     * Get the submission repository instance.
     *
     * @return edulegit_submission_repository The submission repository object.
     */
    public function get_repository(): edulegit_submission_repository {
        return $this->repository;
    }

    /**
     * Get the submission manager instance.
     *
     * @return edulegit_submission_manager The submission manager object.
     */
    public function get_manager(): edulegit_submission_manager {
        return $this->manager;
    }

}
