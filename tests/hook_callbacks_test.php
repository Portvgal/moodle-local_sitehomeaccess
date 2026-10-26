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

namespace local_sitehomeaccess;

use context_course;
use core\hook\after_config;
use local_sitehomeaccess\access\policy;
use local_sitehomeaccess\access\runtime;

/**
 * Tests for Site Home request enforcement callbacks.
 *
 * @package    local_sitehomeaccess
 * @category   test
 * @copyright  2026 Portvgal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_sitehomeaccess\hook_callbacks::after_config
 */
final class hook_callbacks_test extends \advanced_testcase {
    /** @var string Original script path. */
    private string $originalscript;

    /** @var array<string, mixed> Original request server values. */
    private array $originalserver;

    protected function setUp(): void {
        global $SCRIPT;

        parent::setUp();
        $this->resetAfterTest();
        $this->originalscript = $SCRIPT;
        $this->originalserver = $_SERVER;
        runtime::set_test_override(true);
        policy::reset_cache();
        set_config('enabled', 1, 'local_sitehomeaccess');
        set_config('defaultaccess', policy::ACCESS_HIDDEN, 'local_sitehomeaccess');
        set_config('guestaccess', policy::ACCESS_HIDDEN, 'local_sitehomeaccess');
        set_config('cohortexceptions', '', 'local_sitehomeaccess');
        $this->setUser($this->getDataGenerator()->create_user());
    }

    protected function tearDown(): void {
        global $SCRIPT;

        $SCRIPT = $this->originalscript;
        $_SERVER = $this->originalserver;
        runtime::set_test_override(null);
        policy::reset_cache();
        parent::tearDown();
    }

    public function test_unrelated_course_file_request_is_not_blocked(): void {
        $course = $this->getDataGenerator()->create_course();
        $this->set_pluginfile_request(context_course::instance($course->id)->id);

        hook_callbacks::after_config(new after_config());

        $this->addToAssertionCount(1);
    }

    public function test_site_home_file_request_is_blocked(): void {
        $this->set_pluginfile_request(context_course::instance(SITEID)->id);

        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessage(get_string('filenotfound', 'error'));
        @hook_callbacks::after_config(new after_config());
    }

    /**
     * Configure a standard pluginfile request for a context.
     *
     * @param int $contextid Context ID.
     */
    private function set_pluginfile_request(int $contextid): void {
        global $SCRIPT;

        $path = "/{$contextid}/mod_page/content/0/example.txt";
        $SCRIPT = '/pluginfile.php';
        $_SERVER['REQUEST_URI'] = '/pluginfile.php' . $path;
        $_SERVER['PATH_INFO'] = $path;
        $_SERVER['SCRIPT_NAME'] = '/pluginfile.php';
        $_SERVER['SERVER_SOFTWARE'] = 'Apache';
    }
}
