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

namespace tool_customprovider\local;

use tool_customprovider\myprovidertest;

/**
 * Hook callbacks for tool_customprovider
 *
 * @package    tool_customprovider
 * @copyright  2024 Ilya Tregubov
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_callbacks {

    /**
     * Add custom provider
     *
     * @param \customfield_number\hook\add_custom_providers $hook
     */
    public static function add_custom_provider(\customfield_number\hook\add_custom_providers $hook): void {
        $hook->add_provider(new myprovidertest($hook->field));
    }
}
