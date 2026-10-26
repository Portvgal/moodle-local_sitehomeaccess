# Site Home Access Marketplace listing

## Listing details

- **Title:** Site Home Access
- **Component:** `local_sitehomeaccess`
- **Plugin type:** Local plugin
- **Price:** Free
- **Licence:** GNU GPL v3 or later
- **Supported Moodle version:** Moodle 4.5
- **Source code:** https://github.com/Portvgal/moodle-local_sitehomeaccess
- **Issue tracker:** https://github.com/Portvgal/moodle-local_sitehomeaccess/issues
- **Documentation:** https://github.com/Portvgal/moodle-local_sitehomeaccess#readme
- **Current release:** https://github.com/Portvgal/moodle-local_sitehomeaccess/releases/tag/v1.0.1

## Short description

Control who can access Moodle Site Home using cohorts, system roles, and a separate guest policy.

## Full description

Site Home Access lets Moodle administrators control browser access to Site Home without modifying Moodle core. It is intended for sites where Site Home contains navigation, activities, resources, blocks, or course listings meant for selected audiences rather than every visitor.

Administrators choose a default rule for authenticated users and may select one or more cohorts as exceptions. Membership in any selected cohort is enough to match. Access can also be granted through a system-level capability, while guest access is configured independently.

### Features

- Allow Site Home only for selected cohorts and permitted system roles.
- Keep Site Home generally available while excluding selected cohorts.
- Select multiple cohorts; membership in any selected cohort is a match.
- Grant an override through the `local/sitehomeaccess:bypass` capability.
- Configure guest access separately from authenticated-user access.
- Remove Moodle's standard Site Home primary-navigation item for denied users.
- Enforce the same policy for direct Site Home pages, activities, resources, blocks, and standard Moodle file requests.
- Redirect denied authenticated users safely to Dashboard or My courses.
- Preserve administrator recovery access.
- Support realistic testing with Log in as and Switch role.

### Installation

1. Download `moodle-local_sitehomeaccess-1.0.1.zip` from the release page.
2. Install it using Moodle's plugin installer, or extract the `sitehomeaccess` directory to `local/sitehomeaccess`.
3. Complete the Moodle upgrade from Site administration > Notifications.
4. Open Site administration > Plugins > Local plugins > Site Home access.
5. Configure and test the required audiences before enabling enforcement.

The plugin is disabled by default after installation and does not change Site Home access until an administrator enables it.

### Compatibility

- Moodle 4.5.
- PHP and database versions supported by Moodle 4.5.
- No additional Moodle plugins, Composer installation, external services, accounts, API keys, or scheduled tasks are required.
- Automated checks cover PostgreSQL and MariaDB across PHP 8.1, 8.2, and 8.3.

### Privacy and data handling

The plugin stores only its configuration: whether enforcement is enabled, the default authenticated-user rule, the guest rule, and selected cohort IDs. It does not store user IDs, email addresses, copied cohort memberships, access history, analytics, or tracking information. Existing role and cohort membership data is read while evaluating requests. No data is sent to an external service.

The plugin implements Moodle's Privacy API null-provider declaration.

### Support and security

Report functional defects and feature requests through the public GitHub issue tracker. Report security vulnerabilities privately through GitHub Security Advisories. Do not include credentials or personal data in a public issue.

### Known limitations

- The plugin controls standard Moodle browser access to Site Home; it is not a universal security boundary for highly sensitive information.
- Custom themes may render independent Site Home links. Following such a link does not bypass server-side enforcement, but the plugin does not rewrite arbitrary theme navigation.
- Custom download endpoints, Moodle app requests, token-authenticated web services, and third-party plugins that serve content outside standard Moodle browser routes require separate assessment.
- Sites with several thousand cohorts should verify the administration settings page's response time and memory use.

## Third-party dependencies and licensing

The plugin bundles no third-party libraries and requires no external service. Plugin code is licensed under the GNU General Public License, version 3 or later.

## Marketplace artwork checklist

- **Primary icon:** `sitehomeaccess-logo-shield.png` — square transparent PNG without embedded text.
- **Alternative artwork:** `sitehomeaccess-logo-cohorts.png` — square transparent PNG without embedded text.
- **Screenshot 1:** `sitehomeaccess-settings.png` — complete Moodle 4.5 settings page using generic test data.
- **Screenshot 2:** `sitehomeaccess-site-home-navigation.png` — Site Home navigation visible to an allowed administrator using generic test data.
- Confirm the submitter owns or has distribution rights for all uploaded artwork.
- Upload screenshots separately in Marketplace; the `marketplace/` directory is intentionally excluded from the plugin ZIP.

## Final submission checklist

- [x] Public source repository available.
- [x] Public GitHub issue tracker enabled.
- [x] Documentation and support links prepared.
- [x] GPL v3-or-later licence included in the ZIP.
- [x] Version metadata and `v1.0.1` release tag match.
- [x] Validated ZIP uses one top-level `sitehomeaccess/` directory.
- [x] ZIP checksum and archive integrity verified.
- [x] No third-party libraries or external services.
- [x] Privacy handling documented and Privacy API provider included.
- [x] PHPUnit, Behat, PHP lint, PHPCS, PHPDoc, validation, savepoints, Mustache, and frontend CI checks passed.
- [x] PostgreSQL and MariaDB CI passed.
- [x] Marketplace icon and screenshots prepared.
- [ ] Confirm Marketplace provider account and provider terms.
- [ ] Confirm artwork ownership/distribution rights.
- [ ] Upload the `v1.0.1` ZIP, checksum if requested, icon, and screenshots.
- [ ] Enter compatibility as Moodle 4.5 and submit for Marketplace review.
