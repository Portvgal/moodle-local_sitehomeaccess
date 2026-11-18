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
 * Tests for the Site Home Access cohort setting.
 *
 * @package    local_sitehomeaccess
 * @category   test
 * @copyright  2026 Portvgal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_sitehomeaccess;

use context_system;
use local_sitehomeaccess\admin\setting_cohort_multiselect;

/**
 * Tests for the cohort multi-select setting.
 *
 * @package    local_sitehomeaccess
 * @category   test
 * @copyright  2026 Portvgal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_sitehomeaccess\admin\setting_cohort_multiselect
 */
final class setting_cohort_multiselect_test extends \advanced_testcase {
    /**
     * Test that cohort choices retain alphabetical order and context labels.
     */
    public function test_load_choices_includes_cohort_names_and_contexts(): void {
        global $CFG;

        $this->resetAfterTest();
        require_once($CFG->libdir . '/adminlib.php');

        $generator = $this->getDataGenerator();
        $alpha = $generator->create_cohort(['name' => 'Alpha cohort']);
        $zeta = $generator->create_cohort(['name' => 'Zeta cohort']);
        $setting = new setting_cohort_multiselect(
            'local_sitehomeaccess/cohortexceptions',
            'Cohorts',
            '',
            [],
        );

        $this->assertTrue($setting->load_choices());
        $this->assertSame([(int)$alpha->id, (int)$zeta->id], array_keys($setting->choices));
        $this->assertSame(
            'Alpha cohort (' . context_system::instance()->get_context_name(false) . ')',
            $setting->choices[$alpha->id],
        );
        $this->assertSame(
            'Zeta cohort (' . context_system::instance()->get_context_name(false) . ')',
            $setting->choices[$zeta->id],
        );
    }
}
