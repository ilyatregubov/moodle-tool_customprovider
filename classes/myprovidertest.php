<?php
// This file is part of Moodle Workplace https://moodle.com/workplace based on Moodle
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
//
// Moodle Workplace™ Code is the discrete and self-executable
// collection of software scripts (plugins and modifications, and any
// derivations thereof) that are exclusively owned and licensed by
// Moodle Pty Ltd (Moodle) under the terms of its proprietary Moodle
// Workplace License ("MWL") made available with Moodle's open software
// package ("Moodle LMS") offering which itself is freely downloadable
// at "download.moodle.org" and which is provided by Moodle under a
// single GNU General Public License version 3.0, dated 29 June 2007
// ("GPL"). MWL is strictly controlled by Moodle Pty Ltd and its Moodle
// Certified Premium Partners. Wherever conflicting terms exist, the
// terms of the MWL shall prevail.

namespace tool_customprovider;

use context_course;
use customfield_number\provider_base;
use customfield_number\data_controller;
use MoodleQuickForm;

/**
 * Class price
 *
 * @package    customfield_numeric
 * @author     2024 Ilya Tregubov
 * @copyright  2024 Moodle Pty Ltd <support@moodle.com>
 * @license    Moodle Workplace License, distribution is restricted, contact support@moodle.com
 */
class myprovidertest extends provider_base {
    public function get_name(): string {
        return 'Custom provider';
    }

    public function is_available(): bool {
        return $this->field->get_handler()->get_component() === 'core_course' &&
            $this->field->get_handler()->get_area() === 'course';
    }
    public function prepare_export_value($value, ?\context $context = null): ?string {
        $src = 'https://download.moodle.org/unittest/test.jpg';
        $value = '<img src="' . $src .'" alt="test">';
        return $value . '<a class="btn btn-outline-primary me-3 mb-3 btn-insight" href="' . $src . '">link</a>';
    }

    public function recalculate(?int $instanceid = null): void {
        global $DB;
        $displaywhenzero = (bool)$this->field->get_configdata_property('nofactivities_zero');
        $where = '';
        if ($instanceid) {
            $where = "AND c.id = :courseid ";
            $params['courseid'] = $instanceid;
        }
        $sql = "SELECT c.id, 123 AS cnt, d.id AS dataid, d.decvalue
            FROM {course} c
            LEFT JOIN {customfield_data} d ON d.fieldid = :fieldid AND d.instanceid = c.id
            WHERE c.id <> :siteid $where
            GROUP BY c.id, d.id, d.decvalue
        ";
        $params['fieldid'] = $this->field->get('id');
        $records = $DB->get_records_sql($sql, $params + ['siteid' => SITEID]);
        $fieldid = $this->field->get('id');
        foreach ($records as $record) {
            $value = (int)$record->cnt;
            if (!$displaywhenzero && !$value) {
                if ($record->dataid) {
                    (new data_controller((int)$record->dataid, (object)['id' => $record->dataid]))->delete();
                }
            } else if (empty($record->dataid) || round((int)$record->decvalue, 5) != $value) {
                $data = \core_customfield\api::get_instance_fields_data(
                    [$fieldid => $this->field], (int)$record->id)[$fieldid];
                $data->set('contextid', context_course::instance($record->id)->id);
                $data->set('decvalue', $value);
                $data->save();
            }
        }
    }
}