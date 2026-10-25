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

use context_system;
use context_course;
use local_sitehomeaccess\access\decision;
use local_sitehomeaccess\access\policy;
use core\session\manager as session_manager;

/**
 * Tests for Site Home access policy.
 *
 * @package    local_sitehomeaccess
 * @category   test
 * @copyright  2026 Portvgal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_sitehomeaccess\access\policy
 */
final class policy_test extends \advanced_testcase {
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        policy::reset_cache();
        set_config('enabled', 1, 'local_sitehomeaccess');
        set_config('defaultaccess', policy::ACCESS_VISIBLE, 'local_sitehomeaccess');
        set_config('guestaccess', policy::ACCESS_VISIBLE, 'local_sitehomeaccess');
        set_config('cohortexceptions', '', 'local_sitehomeaccess');
    }

    protected function tearDown(): void {
        policy::reset_cache();
        parent::tearDown();
    }

    public function test_disabled_policy_allows_access(): void {
        $this->setUser($this->getDataGenerator()->create_user());
        set_config('enabled', 0, 'local_sitehomeaccess');
        policy::reset_cache();

        $decision = policy::evaluate();

        $this->assertTrue($decision->allowed);
        $this->assertSame(decision::REASON_DISABLED, $decision->reason);
    }

    /**
     * @dataProvider default_access_provider
     * @param string $setting Setting value.
     * @param bool $expected Expected decision.
     */
    public function test_default_access(string $setting, bool $expected): void {
        $this->setUser($this->getDataGenerator()->create_user());
        set_config('defaultaccess', $setting, 'local_sitehomeaccess');
        policy::reset_cache();

        $decision = policy::evaluate();

        $this->assertSame($expected, $decision->allowed);
        $this->assertSame(decision::REASON_DEFAULT, $decision->reason);
    }

    public static function default_access_provider(): array {
        return [
            'visible' => [policy::ACCESS_VISIBLE, true],
            'hidden' => [policy::ACCESS_HIDDEN, false],
        ];
    }

    /**
     * @dataProvider cohort_inversion_provider
     * @param string $setting Default setting.
     * @param bool $expected Expected result for a selected cohort member.
     */
    public function test_selected_cohort_inverts_default(string $setting, bool $expected): void {
        $user = $this->getDataGenerator()->create_user();
        $cohort1 = $this->getDataGenerator()->create_cohort();
        $cohort2 = $this->getDataGenerator()->create_cohort();
        cohort_add_member($cohort2->id, $user->id);
        $this->setUser($user);
        set_config('defaultaccess', $setting, 'local_sitehomeaccess');
        set_config('cohortexceptions', "{$cohort1->id},{$cohort2->id}", 'local_sitehomeaccess');
        policy::reset_cache();

        $decision = policy::evaluate();

        $this->assertSame($expected, $decision->allowed);
        $this->assertSame(decision::REASON_COHORT, $decision->reason);
    }

    public static function cohort_inversion_provider(): array {
        return [
            'visible becomes hidden' => [policy::ACCESS_VISIBLE, false],
            'hidden becomes visible' => [policy::ACCESS_HIDDEN, true],
        ];
    }

    public function test_nonmember_uses_default_and_malformed_ids_are_ignored(): void {
        $this->setUser($this->getDataGenerator()->create_user());
        set_config('defaultaccess', policy::ACCESS_HIDDEN, 'local_sitehomeaccess');
        set_config('cohortexceptions', 'invalid,-2,0,99999999', 'local_sitehomeaccess');
        policy::reset_cache();

        $decision = policy::evaluate();

        $this->assertFalse($decision->allowed);
        $this->assertSame(decision::REASON_DEFAULT, $decision->reason);
    }

    public function test_system_capability_bypasses_hidden_default(): void {
        $user = $this->getDataGenerator()->create_user();
        $roleid = $this->getDataGenerator()->create_role();
        role_assign($roleid, $user->id, context_system::instance());
        assign_capability('local/sitehomeaccess:bypass', CAP_ALLOW, $roleid, context_system::instance());
        $this->setUser($user);
        set_config('defaultaccess', policy::ACCESS_HIDDEN, 'local_sitehomeaccess');
        accesslib_clear_all_caches_for_unit_testing();
        policy::reset_cache();

        $decision = policy::evaluate();

        $this->assertTrue($decision->allowed);
        $this->assertSame(decision::REASON_CAPABILITY, $decision->reason);
    }

    public function test_site_administrator_bypasses_hidden_default(): void {
        $this->setAdminUser();
        set_config('defaultaccess', policy::ACCESS_HIDDEN, 'local_sitehomeaccess');
        policy::reset_cache();

        $decision = policy::evaluate();

        $this->assertTrue($decision->allowed);
        $this->assertSame(decision::REASON_ADMIN, $decision->reason);
    }

    public function test_login_as_evaluates_impersonated_user(): void {
        $target = $this->getDataGenerator()->create_user();
        $this->setAdminUser();
        set_config('defaultaccess', policy::ACCESS_HIDDEN, 'local_sitehomeaccess');
        session_manager::loginas($target->id, context_system::instance(), false);
        policy::reset_cache();

        $decision = policy::evaluate();

        $this->assertFalse($decision->allowed);
        $this->assertSame(decision::REASON_DEFAULT, $decision->reason);
    }

    public function test_login_as_uses_impersonated_users_cohort(): void {
        $target = $this->getDataGenerator()->create_user();
        $cohort = $this->getDataGenerator()->create_cohort();
        cohort_add_member($cohort->id, $target->id);
        $this->setAdminUser();
        set_config('defaultaccess', policy::ACCESS_HIDDEN, 'local_sitehomeaccess');
        set_config('cohortexceptions', (string)$cohort->id, 'local_sitehomeaccess');
        session_manager::loginas($target->id, context_system::instance(), false);
        policy::reset_cache();

        $decision = policy::evaluate();

        $this->assertTrue($decision->allowed);
        $this->assertSame(decision::REASON_COHORT, $decision->reason);
    }

    public function test_manager_archetype_receives_bypass_by_default(): void {
        $user = $this->getDataGenerator()->create_user();
        $roleid = $this->getDataGenerator()->create_role(['archetype' => 'manager']);
        role_assign($roleid, $user->id, context_system::instance());
        $this->setUser($user);
        set_config('defaultaccess', policy::ACCESS_HIDDEN, 'local_sitehomeaccess');
        accesslib_clear_all_caches_for_unit_testing();
        policy::reset_cache();

        $decision = policy::evaluate();

        $this->assertTrue($decision->allowed);
        $this->assertSame(decision::REASON_CAPABILITY, $decision->reason);
    }

    public function test_switched_role_ignores_cohort_membership(): void {
        $user = $this->getDataGenerator()->create_user();
        $cohort = $this->getDataGenerator()->create_cohort();
        cohort_add_member($cohort->id, $user->id);
        $roleid = $this->getDataGenerator()->create_role();
        $this->setUser($user);
        set_config('defaultaccess', policy::ACCESS_HIDDEN, 'local_sitehomeaccess');
        set_config('cohortexceptions', (string)$cohort->id, 'local_sitehomeaccess');
        role_switch($roleid, context_course::instance(SITEID));
        policy::reset_cache();

        $decision = policy::evaluate();

        $this->assertFalse($decision->allowed);
        $this->assertSame(decision::REASON_DEFAULT, $decision->reason);
    }

    public function test_switched_role_can_use_capability(): void {
        $user = $this->getDataGenerator()->create_user();
        $roleid = $this->getDataGenerator()->create_role();
        assign_capability('local/sitehomeaccess:bypass', CAP_ALLOW, $roleid, context_system::instance());
        accesslib_clear_all_caches_for_unit_testing();
        $this->setUser($user);
        set_config('defaultaccess', policy::ACCESS_HIDDEN, 'local_sitehomeaccess');
        role_switch($roleid, context_course::instance(SITEID));
        policy::reset_cache();

        $decision = policy::evaluate();

        $this->assertTrue($decision->allowed);
        $this->assertSame(decision::REASON_CAPABILITY, $decision->reason);
    }

    /**
     * @dataProvider guest_access_provider
     * @param string $setting Guest setting.
     * @param bool $expected Expected decision.
     */
    public function test_guest_access(string $setting, bool $expected): void {
        $this->setGuestUser();
        set_config('guestaccess', $setting, 'local_sitehomeaccess');
        policy::reset_cache();

        $decision = policy::evaluate();

        $this->assertSame($expected, $decision->allowed);
        $this->assertSame(decision::REASON_GUEST, $decision->reason);
    }

    public static function guest_access_provider(): array {
        return [
            'visible' => [policy::ACCESS_VISIBLE, true],
            'hidden' => [policy::ACCESS_HIDDEN, false],
        ];
    }
}

