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

use core\hook\after_config;
use core\hook\navigation\primary_extend;
use core\hook\output\before_http_headers;
use core\notification;
use local_sitehomeaccess\access\policy;
use local_sitehomeaccess\access\protected_context;
use local_sitehomeaccess\access\runtime;
use moodle_url;

/**
 * Hook callbacks for Site Home Access.
 *
 * @package    local_sitehomeaccess
 * @copyright  2026 Portvgal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class hook_callbacks {
    /**
     * Remove Site Home from primary navigation when access is denied.
     *
     * @param primary_extend $hook Hook instance.
     */
    public static function extend_primary_navigation(primary_extend $hook): void {
        if (!runtime::should_enforce()) {
            return;
        }
        if (policy::evaluate()->allowed) {
            return;
        }

        $home = $hook->get_primaryview()->get('home');
        if ($home) {
            $home->remove();
        }
    }

    /**
     * Enforce access on normal rendered browser pages.
     *
     * @param before_http_headers $hook Hook instance.
     */
    public static function before_http_headers(before_http_headers $hook): void {
        global $CFG, $SESSION;

        if (!runtime::should_enforce()) {
            return;
        }
        $page = $hook->renderer->get_page();
        if (!protected_context::contains($page->context)) {
            return;
        }

        $decision = policy::evaluate();
        if ($decision->allowed) {
            return;
        }

        if (!isloggedin() || isguestuser()) {
            $SESSION->wantsurl = qualified_me();
            redirect(get_login_url());
        }

        notification::add(get_string('accessdeniednotification', 'local_sitehomeaccess'), notification::WARNING);
        $destination = !empty($CFG->enabledashboard) ? new moodle_url('/my/') : new moodle_url('/my/courses.php');
        redirect($destination);
    }

    /**
     * Protect standard Site Home files, which do not render a page header.
     *
     * @param after_config $hook Hook instance.
     */
    public static function after_config(after_config $hook): void {
        global $CFG, $SCRIPT;

        unset($hook);

        if (!runtime::should_enforce() || $SCRIPT !== '/pluginfile.php') {
            return;
        }

        $relativepath = get_file_argument();
        if (!$relativepath || $relativepath[0] !== '/') {
            return;
        }

        $parts = explode('/', ltrim($relativepath, '/'));
        $contextid = (int)($parts[0] ?? 0);
        if (!protected_context::context_id_is_protected($contextid)) {
            return;
        }

        if (policy::evaluate()->allowed) {
            return;
        }

        require_once($CFG->libdir . '/filelib.php');
        send_file_not_found();
    }
}
