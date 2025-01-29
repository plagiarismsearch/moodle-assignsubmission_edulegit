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
 * The assignsubmission_edulegit config class.
 *
 * This class handles the configuration settings for the assignsubmission_edulegit plugin.
 *
 * @package   assignsubmission_edulegit
 * @author    Alex Crosby <developer@edulegit.com>
 * @copyright @2024 EduLegit.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignsubmission_edulegit;

/**
 * Class edulegit_config
 *
 * Manages configuration for the assignsubmission_edulegit plugin.
 */
class edulegit_config {

    /**
     * Stores the plugin-specific configuration.
     *
     * @var \stdClass|null
     */
    private ?\stdClass $config = null;

    /**
     * Initializes the config class with a specific configuration object.
     *
     * @param \stdClass $config The configuration object for the plugin.
     */
    public function __construct(\stdClass $config) {
        $this->config = $config;
    }

    /**
     * Retrieves a specific config value by name.
     *
     * @param string $name The name of the configuration setting.
     * @param mixed $default The default value if the config setting is not found.
     * @return mixed The configuration value or the default if not found.
     */
    public function get($name, $default = null) {
        $value = $this->config->{$name} ?? false;
        return $value !== false ? $value : $default;
    }

    /**
     * Retrieves a global configuration value.
     *
     * @param string $name The name of the global configuration setting.
     * @param mixed $default The default value if the global config setting is not found.
     * @return mixed The global configuration value or the default if not found.
     */
    public function get_global($name, $default = null) {
        $value = get_config('assignsubmission_edulegit', $name);
        return $value !== false ? $value : $default;
    }

    /**
     * Retrieves a configuration value from either the plugin or global config.
     *
     * First attempts to retrieve a configuration setting from the plugin's config.
     * If not found, it defaults to the global Moodle configuration.
     *
     * @param string $name The name of the configuration setting.
     * @param mixed $default The default value if neither the plugin nor global config contains the setting.
     * @return mixed The configuration value or the default if not found.
     */
    public function get_plugin_or_global_config($name, $default = null) {
        return $this->get($name, $this->get_global($name, $default));
    }

    /**
     * Retrieves the Moodle release version.
     *
     * @return string|null The Moodle version or release, or null if not found.
     */
    public function get_release() {
        global $CFG;
        if (isset($CFG->version)) {
            return $CFG->version;
        }
        if (isset($CFG->release)) {
            return $CFG->release;
        }
        return null;
    }

    /**
     * Retrieves the plugin's release version.
     *
     * @return mixed The plugin's version, or null if not found.
     */
    public function get_plugin_release() {
        return $this->get_global('version');
    }

}
