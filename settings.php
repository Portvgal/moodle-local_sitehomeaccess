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
 * Site Home Access settings.
 *
 * @package    local_sitehomeaccess
 * @copyright  2026 Portvgal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    global $DB;

    $settings = new admin_settingpage('local_sitehomeaccess', get_string('pluginname', 'local_sitehomeaccess'));

    $settings->add(new admin_setting_configcheckbox(
        'local_sitehomeaccess/enabled',
        get_string('enabled', 'local_sitehomeaccess'),
        get_string('enabled_desc', 'local_sitehomeaccess'),
        0,
    ));

    $settings->add(new admin_setting_configselect(
        'local_sitehomeaccess/defaultaccess',
        get_string('defaultaccess', 'local_sitehomeaccess'),
        get_string('defaultaccess_desc', 'local_sitehomeaccess'),
        \local_sitehomeaccess\access\policy::ACCESS_VISIBLE,
        [
            \local_sitehomeaccess\access\policy::ACCESS_VISIBLE =>
                get_string('accessmode_all_except', 'local_sitehomeaccess'),
            \local_sitehomeaccess\access\policy::ACCESS_HIDDEN =>
                get_string('accessmode_selected_only', 'local_sitehomeaccess'),
        ],
    ));

    $defaultaccess = get_config('local_sitehomeaccess', 'defaultaccess');
    $cohortdescription = $defaultaccess === \local_sitehomeaccess\access\policy::ACCESS_HIDDEN
        ? get_string('cohortexceptions_allow_desc', 'local_sitehomeaccess')
        : get_string('cohortexceptions_deny_desc', 'local_sitehomeaccess');

    if ($DB->count_records('cohort') > 0) {
        $settings->add(new \local_sitehomeaccess\admin\setting_cohort_multiselect(
            'local_sitehomeaccess/cohortexceptions',
            get_string('cohortexceptions', 'local_sitehomeaccess'),
            $cohortdescription,
            [],
        ));
    } else {
        $manageurl = new moodle_url('/cohort/index.php', ['contextid' => context_system::instance()->id]);
        $managelink = html_writer::link($manageurl, get_string('managecohorts', 'local_sitehomeaccess'));
        $settings->add(new admin_setting_heading(
            'local_sitehomeaccess/nocohorts',
            get_string('cohortexceptions', 'local_sitehomeaccess'),
            get_string('nocohorts_desc', 'local_sitehomeaccess', $managelink),
        ));
    }

    $settings->add(new admin_setting_configselect(
        'local_sitehomeaccess/guestaccess',
        get_string('guestaccess', 'local_sitehomeaccess'),
        get_string('guestaccess_desc', 'local_sitehomeaccess'),
        \local_sitehomeaccess\access\policy::ACCESS_VISIBLE,
        [
            \local_sitehomeaccess\access\policy::ACCESS_VISIBLE => get_string('guestaccess_allow', 'local_sitehomeaccess'),
            \local_sitehomeaccess\access\policy::ACCESS_HIDDEN => get_string('guestaccess_login', 'local_sitehomeaccess'),
        ],
    ));

    $rolesurl = new moodle_url('/admin/roles/manage.php');
    $roleslink = html_writer::link($rolesurl, get_string('manageroles', 'local_sitehomeaccess'));
    $settings->add(new admin_setting_heading(
        'local_sitehomeaccess/capabilityheading',
        get_string('capabilityheading', 'local_sitehomeaccess'),
        get_string('capabilityheading_desc', 'local_sitehomeaccess', $roleslink),
    ));

    $ADMIN->add('localplugins', $settings);
}

