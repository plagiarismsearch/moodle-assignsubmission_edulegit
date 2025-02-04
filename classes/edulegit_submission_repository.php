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
 * The assignsubmission_edulegit submission repository class.
 *
 * @package   assignsubmission_edulegit
 * @author    Alex Crosby <developer@edulegit.com>
 * @copyright @2024 EduLegit.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignsubmission_edulegit;

/**
 * Class edulegit_submission_repository
 *
 * Handles database operations related to EduLegit submissions.
 */
class edulegit_submission_repository {

    /** @var string The database table name for EduLegit submissions. */
    const EDULEGIT_SUBMISSION_TABLE_NAME = 'assignsubmission_edulegit';

    /** @var \moodle_database The Moodle database object. */
    private $db;

    /**
     * Constructor for edulegit_submission_repository.
     */
    public function __construct() {
        global $DB;
        $this->db = $DB;
    }

    /**
     * Retrieves a submission by its id.
     *
     * @param int $id The ID of the submission.
     * @return edulegit_submission_entity|null The submission entity or null if not found.
     */
    public function get_by_id(int $id): ?edulegit_submission_entity {
        $submission = $this->db->get_record(self::EDULEGIT_SUBMISSION_TABLE_NAME, ['id' => $id]);
        return $submission ? new edulegit_submission_entity($submission) : null;
    }

    /**
     * Retrieves a submission by its submission id.
     *
     * @param int $submissionid The submission id.
     * @return edulegit_submission_entity|null The submission entity or null if not found.
     */
    public function get_submission(int $submissionid): ?edulegit_submission_entity {
        $submission = $this->db->get_record(self::EDULEGIT_SUBMISSION_TABLE_NAME, ['submission' => $submissionid]);
        return $submission ? new edulegit_submission_entity($submission) : null;
    }

    /**
     * Retrieves the task user IDs associated with a specific submission.
     *
     * @param int $submissionid The ID of the submission.
     * @return int[] An array of unique task user IDs associated with the submission.
     */
    public function get_submission_task_user_ids(int $submissionid): array {
        return $this->get_task_user_ids(['submission' => $submissionid]);
    }

    /**
     * Retrieves the task user IDs associated with a specific assignment.
     *
     * @param int $assignmentid The ID of the assignment.
     * @return int[] An array of unique task user IDs associated with the assignment.
     */
    public function get_assignment_task_user_ids(int $assignmentid): array {
        return $this->get_task_user_ids(['assignment' => $assignmentid]);
    }

    /**
     * Retrieves task user IDs based on specified conditions.
     *
     * @param array $conditions An associative array of conditions for filtering records.
     * @return int[] An array of unique task user IDs matching the conditions.
     */
    private function get_task_user_ids(array $conditions): array {
        $result = [];
        $submissions = $this->db->get_records(self::EDULEGIT_SUBMISSION_TABLE_NAME, $conditions, '', 'taskuserid');

        foreach ($submissions as $submission) {
            $result[] = $submission->taskuserid;
        }
        return array_unique($result);
    }

    /**
     * Deletes a submission by its submission id.
     *
     * @param int $submissionid The submission id.
     * @return bool True on success, false on failure.
     */
    public function delete_submission(int $submissionid): bool {
        return $this->db->delete_records(self::EDULEGIT_SUBMISSION_TABLE_NAME, ['submission' => $submissionid]);
    }

    /**
     * Deletes all submissions related to a specific assignment id.
     *
     * @param int $assignmentid The assignment id.
     * @return bool True on success, false on failure.
     */
    public function delete_assignment(int $assignmentid): bool {
        return $this->db->delete_records(self::EDULEGIT_SUBMISSION_TABLE_NAME, ['assignment' => $assignmentid]);
    }

    /**
     * Inserts a new submission record into the database.
     *
     * @param edulegit_submission_entity $edulegitsubmission The submission entity to insert.
     * @return int|false The inserted record's ID or false on failure.
     */
    public function insert_submission(edulegit_submission_entity $edulegitsubmission) {
        $edulegitsubmission->createdat ??= time();
        $edulegitsubmission->updatedat ??= time();
        return $this->db->insert_record(self::EDULEGIT_SUBMISSION_TABLE_NAME, $edulegitsubmission);
    }

    /**
     * Updates an existing submission record in the database.
     *
     * @param edulegit_submission_entity $edulegitsubmission The submission entity to update.
     * @return bool True on success, false on failure.
     */
    public function update_submission(edulegit_submission_entity $edulegitsubmission): bool {
        $edulegitsubmission->updatedat = time();
        return $this->db->update_record(self::EDULEGIT_SUBMISSION_TABLE_NAME, $edulegitsubmission);
    }

    /**
     * Retrieves assignment information by assignment id.
     *
     * @param int $assignmentid The assignment id.
     * @return object|null The assignment information or null if not found.
     */
    public function get_assignment_info(int $assignmentid) {
        $params = ['id' => $assignmentid];
        $sql = "SELECT a.id, a.course, a.name, a.intro, a.duedate, a.allowsubmissionsfromdate, a.gradingduedate, a.activity,
                c.shortname AS course_shortname, c.fullname AS course_fullname, c.summary AS course_summary,
                c.startdate AS course_startdate, c.enddate AS course_enddate
                FROM {assign} a
                JOIN {course} c ON c.id = a.course
                WHERE a.id = :id";

        return $this->db->get_record_sql($sql, $params);
    }

}
