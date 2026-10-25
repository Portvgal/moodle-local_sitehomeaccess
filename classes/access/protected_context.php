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

namespace local_sitehomeaccess\access;

use context;

/**
 * Detects contexts belonging to Site Home.
 *
 * @package    local_sitehomeaccess
 * @copyright  2026 Portvgal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class protected_context {
    /**
     * Whether a context represents Site Home or content placed on it.
     *
     * @param context $context Context to inspect.
     * @return bool
     */
    public static function contains(context $context): bool {
        if ((int)$context->contextlevel === CONTEXT_COURSE) {
            return (int)$context->instanceid === (int)SITEID;
        }

        if (!in_array((int)$context->contextlevel, [CONTEXT_MODULE, CONTEXT_BLOCK], true)) {
            return false;
        }

        $coursecontext = $context->get_course_context(false);
        return $coursecontext instanceof context
            && (int)$coursecontext->contextlevel === CONTEXT_COURSE
            && (int)$coursecontext->instanceid === (int)SITEID;
    }

    /**
     * Resolve and inspect a context ID without throwing for stale IDs.
     *
     * @param int $contextid Context ID.
     * @return bool
     */
    public static function context_id_is_protected(int $contextid): bool {
        if ($contextid <= 0) {
            return false;
        }

        $context = context::instance_by_id($contextid, IGNORE_MISSING);
        return $context instanceof context && self::contains($context);
    }
}
