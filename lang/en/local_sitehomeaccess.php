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
 * English language strings for Site Home Access.
 *
 * @package    local_sitehomeaccess
 * @copyright  2026 Portvgal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['accessdeniednotification'] = 'Site Home is not available for your account.';
$string['accessmode_all_except'] = 'Everyone except members of selected cohorts';
$string['accessmode_selected_only'] = 'Only members of selected cohorts and permitted system roles';
$string['capabilityheading'] = 'Access through system roles';
$string['capabilityheading_desc'] = 'Site administrators can always access Site Home. Other users can also have access when one of their system roles has the “Always access Site Home” permission. {$a}.';
$string['cohortchoice'] = '{$a->name} ({$a->context})';
$string['cohortexceptions'] = 'Cohorts';
$string['cohortexceptions_allow_desc'] = 'Select the cohorts whose members may access Site Home. Site administrators and users with a permitted system role can also access it.';
$string['cohortexceptions_deny_desc'] = 'Select the cohorts whose members should be blocked from Site Home. Site administrators and users with a permitted system role will still have access.';
$string['defaultaccess'] = 'Who can access Site Home?';
$string['defaultaccess_desc'] = 'Choose the general rule for signed-in users, then use the Cohorts setting to select the cohorts included in the rule.';
$string['enabled'] = 'Restrict access to Site Home';
$string['enabled_desc'] = 'Turn on the access rules below. When this is off, the plugin does not change Moodle access or navigation.';
$string['guestaccess'] = 'Visitors who are not logged in';
$string['guestaccess_allow'] = 'Allow access to Site Home';
$string['guestaccess_desc'] = 'Choose whether visitors can see Site Home before they log in.';
$string['guestaccess_login'] = 'Require them to log in';
$string['local/sitehomeaccess:bypass'] = 'Always access Site Home';
$string['managecohorts'] = 'Manage cohorts';
$string['manageroles'] = 'Manage system role permissions';
$string['nocohorts_desc'] = 'No cohorts are available. {$a}, create at least one cohort, then return to this page to select cohort exceptions.';
$string['pluginname'] = 'Site Home access';
$string['privacy:metadata'] = 'The Site Home access plugin does not store personal data. It reads existing role and cohort membership information only while evaluating access.';
