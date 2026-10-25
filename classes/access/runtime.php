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

/**
 * Determines whether request-time enforcement is safe in this execution mode.
 *
 * @package    local_sitehomeaccess
 * @copyright  2026 Portvgal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class runtime {
    /** @var bool|null Test-only enforcement override. */
    private static ?bool $testoverride = null;

    /**
     * Whether hooks should enforce access in this request.
     *
     * @return bool
     */
    public static function should_enforce(): bool {
        global $CFG;

        if (defined('PHPUNIT_TEST') && PHPUNIT_TEST && self::$testoverride !== null) {
            return self::$testoverride;
        }

        foreach (['CLI_SCRIPT', 'PHPUNIT_TEST', 'WS_SERVER'] as $constant) {
            if (self::constant_is_true($constant)) {
                return false;
            }
        }

        return !during_initial_install() && empty($CFG->upgraderunning);
    }

    /**
     * Whether a runtime-mode constant is defined and enabled.
     *
     * @param string $name Constant name.
     * @return bool
     */
    private static function constant_is_true(string $name): bool {
        return defined($name) && (bool)constant($name);
    }

    /**
     * Override enforcement during PHPUnit tests.
     *
     * @param bool|null $value Override, or null to restore normal behaviour.
     */
    public static function set_test_override(?bool $value): void {
        if (!defined('PHPUNIT_TEST') || !PHPUNIT_TEST) {
            throw new \coding_exception('The runtime override is available only during PHPUnit tests.');
        }
        self::$testoverride = $value;
    }
}
