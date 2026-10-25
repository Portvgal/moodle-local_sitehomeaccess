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

use context_course;
use context_system;
use core\session\manager as session_manager;

/**
 * Evaluates Site Home access policy.
 *
 * @package    local_sitehomeaccess
 * @copyright  2026 Portvgal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class policy {
    /** @var string Visible setting value. */
    public const ACCESS_VISIBLE = 'visible';
    /** @var string Hidden setting value. */
    public const ACCESS_HIDDEN = 'hidden';

    /** @var array<string, decision> Request-local decisions. */
    private static array $decisioncache = [];

    /**
     * Evaluate access for the current user.
     *
     * @return decision
     */
    public static function evaluate(): decision {
        global $DB, $USER;

        $user = $USER;
        $config = get_config('local_sitehomeaccess');
        $switchedrole = !empty($user->id) && is_role_switched(SITEID);
        $loggedinas = session_manager::is_loggedinas();
        $cachekey = implode(':', [
            (int)($user->id ?? 0),
            (int)!empty($config->enabled),
            (string)($config->defaultaccess ?? self::ACCESS_VISIBLE),
            (string)($config->guestaccess ?? self::ACCESS_VISIBLE),
            (string)($config->cohortexceptions ?? ''),
            (int)$switchedrole,
            (int)$loggedinas,
        ]);

        if (isset(self::$decisioncache[$cachekey])) {
            return self::$decisioncache[$cachekey];
        }

        if (empty($config->enabled)) {
            return self::$decisioncache[$cachekey] = new decision(true, decision::REASON_DISABLED);
        }

        if (empty($user->id) || isguestuser($user)) {
            $allowed = ($config->guestaccess ?? self::ACCESS_VISIBLE) !== self::ACCESS_HIDDEN;
            return self::$decisioncache[$cachekey] = new decision($allowed, decision::REASON_GUEST);
        }

        if (!$loggedinas && !$switchedrole && is_siteadmin($user)) {
            return self::$decisioncache[$cachekey] = new decision(true, decision::REASON_ADMIN);
        }

        $capabilitycontext = $switchedrole ? context_course::instance(SITEID) : context_system::instance();
        if (has_capability('local/sitehomeaccess:bypass', $capabilitycontext, $user)) {
            return self::$decisioncache[$cachekey] = new decision(true, decision::REASON_CAPABILITY);
        }

        $defaultallowed = ($config->defaultaccess ?? self::ACCESS_VISIBLE) !== self::ACCESS_HIDDEN;

        // Cohorts are deliberately ignored while switching role so the effective role can be tested.
        if (!$switchedrole) {
            $cohortids = self::get_configured_cohort_ids((string)($config->cohortexceptions ?? ''));
            if ($cohortids) {
                [$insql, $params] = $DB->get_in_or_equal($cohortids, SQL_PARAMS_NAMED, 'cohort');
                $params['userid'] = (int)$user->id;
                if ($DB->record_exists_select('cohort_members', "userid = :userid AND cohortid {$insql}", $params)) {
                    return self::$decisioncache[$cachekey] = new decision(!$defaultallowed, decision::REASON_COHORT);
                }
            }
        }

        return self::$decisioncache[$cachekey] = new decision($defaultallowed, decision::REASON_DEFAULT);
    }

    /**
     * Clear request-local state, primarily for tests.
     */
    public static function reset_cache(): void {
        self::$decisioncache = [];
    }

    /**
     * Parse configured cohort IDs safely.
     *
     * @param string $value Comma-separated configuration value.
     * @return int[]
     */
    private static function get_configured_cohort_ids(string $value): array {
        $ids = array_filter(array_map('trim', explode(',', $value)), static function(string $id): bool {
            return ctype_digit($id) && (int)$id > 0;
        });

        return array_values(array_unique(array_map('intval', $ids)));
    }

}

