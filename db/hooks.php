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
 * Hook registrations for Site Home Access.
 *
 * @package    local_sitehomeaccess
 * @copyright  2026 Portvgal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$callbacks = [
    [
        'hook' => \core\hook\navigation\primary_extend::class,
        'callback' => [\local_sitehomeaccess\hook_callbacks::class, 'extend_primary_navigation'],
    ],
    [
        'hook' => \core\hook\output\before_http_headers::class,
        'callback' => [\local_sitehomeaccess\hook_callbacks::class, 'before_http_headers'],
    ],
    [
        'hook' => \core\hook\after_config::class,
        'callback' => [\local_sitehomeaccess\hook_callbacks::class, 'after_config'],
    ],
];
