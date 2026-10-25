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
 * Immutable Site Home access decision.
 *
 * Reasons are intended for tests and diagnostics, never for end-user output.
 *
 * @package    local_sitehomeaccess
 * @copyright  2026 Portvgal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class decision {
    /** @var string Plugin is disabled. */
    public const REASON_DISABLED = 'disabled';
    /** @var string Guest policy was used. */
    public const REASON_GUEST = 'guest';
    /** @var string Site administrator safety bypass was used. */
    public const REASON_ADMIN = 'admin';
    /** @var string Capability bypass was used. */
    public const REASON_CAPABILITY = 'capability';
    /** @var string A selected cohort inverted the default. */
    public const REASON_COHORT = 'cohort';
    /** @var string The configured default was used. */
    public const REASON_DEFAULT = 'default';

    /**
     * Constructor.
     *
     * @param bool $allowed Whether access is allowed.
     * @param string $reason Machine-readable reason.
     */
    public function __construct(
        /** @var bool Whether access is allowed. */
        public readonly bool $allowed,
        /** @var string Machine-readable reason. */
        public readonly string $reason,
    ) {
    }
}
