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

namespace local_sitehomeaccess\admin;

use admin_setting_configmultiselect;
use context;

/**
 * Searchable cohort multi-select setting.
 *
 * @package    local_sitehomeaccess
 * @copyright  2026 Portvgal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class setting_cohort_multiselect extends admin_setting_configmultiselect {
    /**
     * Constructor.
     *
     * @param string $name Setting name.
     * @param string $visiblename Visible name.
     * @param string $description Description.
     * @param array $defaultsetting Default cohort IDs.
     */
    public function __construct(string $name, string $visiblename, string $description, array $defaultsetting) {
        parent::__construct($name, $visiblename, $description, $defaultsetting, null);
    }

    /**
     * Load current cohorts as choices.
     *
     * @return bool
     */
    public function load_choices(): bool {
        global $DB;

        if (is_array($this->choices)) {
            return true;
        }

        $this->choices = [];
        $cohorts = $DB->get_records('cohort', null, 'name ASC, id ASC', 'id, name, contextid');
        foreach ($cohorts as $cohort) {
            $context = context::instance_by_id((int)$cohort->contextid, IGNORE_MISSING);
            if (!$context) {
                continue;
            }
            $name = format_string($cohort->name, true, ['context' => $context]);
            $this->choices[(int)$cohort->id] = get_string('cohortchoice', 'local_sitehomeaccess', (object)[
                'name' => $name,
                'context' => $context->get_context_name(false),
            ]);
        }

        return true;
    }

    /**
     * Render the multi-select and enable Moodle autocomplete.
     *
     * @param mixed $data Current value.
     * @param string $query Settings search query.
     * @return string
     */
    public function output_html($data, $query = ''): string {
        global $PAGE;

        $html = parent::output_html($data, $query);
        if ($html !== '') {
            $PAGE->requires->js_call_amd('core/form-autocomplete', 'enhance', [
                '#' . $this->get_id(),
                false,
                '',
                get_string('search'),
                false,
                true,
                '',
            ]);
        }
        return $html;
    }
}
