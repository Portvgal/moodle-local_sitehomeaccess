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

use core\hook\navigation\primary_extend;
use local_sitehomeaccess\access\policy;
use local_sitehomeaccess\access\runtime;

/**
 * Tests for primary navigation handling.
 *
 * @package    local_sitehomeaccess
 * @category   test
 * @copyright  2026 Portvgal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_sitehomeaccess\hook_callbacks::extend_primary_navigation
 */
final class navigation_test extends \advanced_testcase {
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        runtime::set_test_override(true);
        policy::reset_cache();
        set_config('enabled', 1, 'local_sitehomeaccess');
        set_config('guestaccess', policy::ACCESS_VISIBLE, 'local_sitehomeaccess');
        set_config('cohortexceptions', '', 'local_sitehomeaccess');
    }

    protected function tearDown(): void {
        runtime::set_test_override(null);
        policy::reset_cache();
        parent::tearDown();
    }

    public function test_home_node_is_removed_when_denied(): void {
        global $PAGE;

        $this->setUser($this->getDataGenerator()->create_user());
        set_config('defaultaccess', policy::ACCESS_HIDDEN, 'local_sitehomeaccess');
        policy::reset_cache();
        $PAGE->set_url('/');
        $view = new \core\navigation\views\primary($PAGE);
        $view->add('Home', new \moodle_url('/'), \navigation_node::TYPE_SYSTEM, null, 'home');
        $view->add('Dashboard', new \moodle_url('/my/'), \navigation_node::TYPE_SYSTEM, null, 'myhome');

        hook_callbacks::extend_primary_navigation(new primary_extend($view));

        $this->assertFalse($view->get('home'));
        $this->assertNotNull($view->get('myhome'));
    }

    public function test_home_node_remains_when_allowed(): void {
        global $PAGE;

        $this->setUser($this->getDataGenerator()->create_user());
        set_config('defaultaccess', policy::ACCESS_VISIBLE, 'local_sitehomeaccess');
        policy::reset_cache();
        $PAGE->set_url('/');
        $view = new \core\navigation\views\primary($PAGE);
        $view->add('Home', new \moodle_url('/'), \navigation_node::TYPE_SYSTEM, null, 'home');

        hook_callbacks::extend_primary_navigation(new primary_extend($view));

        $this->assertNotNull($view->get('home'));
    }
}

