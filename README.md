# Site Home Access

Site Home Access (`local_sitehomeaccess`) controls browser access to Moodle's Site Home without modifying Moodle core.

## Requirements

- Moodle 4.5.
- PHP and database versions supported by the installed Moodle release.

## Installation

Place the plugin in `local/sitehomeaccess`, then complete Moodle's normal plugin installation process. The access policy is disabled after installation, so existing behaviour does not change until an administrator configures and enables it.

## Configuration

Open **Site administration > Plugins > Local plugins > Site Home access**.

1. Under **Who can access Site Home?**, choose one of these rules:
   - **Only members of selected cohorts and permitted system roles**.
   - **Everyone except members of selected cohorts**.
2. Select zero or more cohorts. If the selector is not yet available, use the displayed **Manage cohorts** link to create a cohort first.
3. Choose guest access independently.
4. Optionally grant `local/sitehomeaccess:bypass` to a role at system context.
5. Enable **Restrict access to Site Home** after testing the configuration.

Site administrators can always access Site Home. Other users can also have access when one of their system roles has the `local/sitehomeaccess:bypass` permission. Review and change this through Moodle role management.

Site administrators retain access during normal sessions. When using **Log in as**, the impersonated user's policy is evaluated. When using **Switch role** on Site Home, administrator and cohort bypasses are ignored so the effective role and default policy can be tested.

Denied authenticated users are redirected to Dashboard, or My courses when Dashboard is disabled. Denied guests are sent to the normal login page.

## Protected browser access

The plugin removes Moodle's primary Site Home navigation node and protects standard browser routes for Site Home, Site Home sections, activities and resources placed on Site Home, and files served through `pluginfile.php` in Site Home contexts.

The site logo, short site name, or a custom theme may still render a link to Site Home. Such a link does not bypass access enforcement.

## Limitations

This plugin is browser access control, not a universal security boundary. It cannot guarantee interception of:

- every custom endpoint implemented by third-party activity plugins;
- official Moodle app requests; or
- generic token-authenticated web-service calls.

Do not place highly sensitive material on Site Home solely in reliance on this plugin.

## Emergency recovery

If an incorrect policy prevents access, disable it from the Moodle command line:

```sh
php admin/cli/cfg.php --component=local_sitehomeaccess --name=enabled --set=0
```

If command-line configuration is unavailable, temporarily rename the `local/sitehomeaccess` directory and complete Moodle's normal recovery process.

## Privacy

The plugin stores only configuration values and selected cohort IDs. It does not store user IDs, email addresses, copied memberships, decision history, or analytics data, and it sends no data to external services.

## Licence and copyright

Copyright © 2026 Portvgal.

This plugin is licensed under the GNU General Public License, version 3 or later. See `LICENSE` for the full licence text.

## Release packaging

The distributable archive must contain a single top-level directory named `sitehomeaccess`. Install that directory at `local/sitehomeaccess` in Moodle 4.5.

Report defects through the repository issue tracker after the public repository has been created. Do not include credentials, personal data, or other sensitive information in a report.

