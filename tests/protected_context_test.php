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
use context_block;
use context_module;
use context_system;
use local_sitehomeaccess\access\protected_context;

/**
 * Tests for protected context detection.
 *
 * @package    local_sitehomeaccess
 * @category   test
 * @copyright  2026 Portvgal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_sitehomeaccess\access\protected_context
 */
final class protected_context_test extends \advanced_testcase {
    public function test_site_course_context_is_protected(): void {
        $context = context_course::instance(SITEID);
        $this->assertTrue(protected_context::contains($context), json_encode([
            'siteid' => SITEID,
            'class' => get_class($context),
            'contextlevel' => $context->contextlevel,
            'instanceid' => $context->instanceid,
        ]));
    }

    public function test_system_context_is_not_protected(): void {
        $this->assertFalse(protected_context::contains(context_system::instance()));
    }

    public function test_ordinary_course_and_module_are_not_protected(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $page = $this->getDataGenerator()->create_module('page', ['course' => $course->id]);

        $this->assertFalse(protected_context::contains(context_course::instance($course->id)));
        $this->assertFalse(protected_context::contains(context_module::instance($page->cmid)));
    }

    public function test_site_home_module_is_protected(): void {
        $this->resetAfterTest();
        $page = $this->getDataGenerator()->create_module('page', ['course' => SITEID]);
        $context = context_module::instance($page->cmid);

        $this->assertTrue(protected_context::contains($context), json_encode([
            'siteid' => SITEID,
            'class' => get_class($context),
            'contextlevel' => $context->contextlevel,
            'instanceid' => $context->instanceid,
            'coursecontext' => $context->get_course_context(false)?->instanceid,
        ]));
        $this->assertTrue(protected_context::context_id_is_protected($context->id));
    }

    public function test_site_home_block_is_protected(): void {
        $this->resetAfterTest();
        $block = $this->getDataGenerator()->create_block('online_users', [
            'parentcontextid' => context_course::instance(SITEID)->id,
        ]);
        $context = context_block::instance($block->id);

        $this->assertTrue(protected_context::contains($context));
        $this->assertTrue(protected_context::context_id_is_protected($context->id));
    }

    public function test_ordinary_course_block_is_not_protected(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $block = $this->getDataGenerator()->create_block('online_users', [
            'parentcontextid' => context_course::instance($course->id)->id,
        ]);

        $this->assertFalse(protected_context::contains(context_block::instance($block->id)));
    }

    public function test_missing_context_is_not_protected(): void {
        $this->assertFalse(protected_context::context_id_is_protected(99999999));
    }
}
