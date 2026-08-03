# ⚡ User Management Panel

A PHP user management panel with optional invite-only registration, subscriptions, a protected client download and administration tools.

## Requirements

- PHP 8.0 or newer
- MySQL or MariaDB
- PHP extensions: PDO MySQL, cURL, JSON, Session, Filter, Hash and Ctype
- Apache or nginx
- Write access for `app/core` during setup and `usercontent/avatar` while the panel is running. If the host blocks config writes, the wizard provides the completed PHP config for upload.

The setup page checks the current server before making changes. It works on a VPS as well as regular shared webspace and does not require Apache rewrite rules. The included `.htaccess` protects configuration and SQL files on Apache.

## Installation

1. Upload the contents of `src` to the directory from which the panel should run.
2. Place `DB.sql` and `migration.sql` either beside `setup.php`, one directory above it, or inside an `install` directory.
3. Open `setup.php` in your browser.
4. Select a fresh installation, enter the database and panel details, and create the first administrator.
5. Keep `app/core/setup.lock` in place after a successful installation.

The installer can create an empty database when the supplied database user has permission. On more restricted webspaces, create the database in the hosting control panel first and uncheck automatic creation.

If PHP cannot write to `app/core`, use **Download LocalConfig.php**, upload that file into `app/core`, and run the installer again. The application can use the included config templates directly, so no manual file renaming is required. Remove both setup files immediately if the host also prevents creation of the setup lock.

## Updating an existing installation

The setup wizard does not download panel releases by itself. Upload the new files first using FTP/SFTP or your hosting control panel. On a VPS you can alternatively deploy them with Git. Do not overwrite `app/core/LocalConfig.php`.

1. Back up the panel database.
2. Upload the new panel files and the current combined `migration.sql`.
3. Remove `app/core/setup.lock` intentionally.
4. Open `setup.php` and select **Upgrade existing installation**.
5. Check the detected settings and start the upgrade.
6. The wizard applies `migration.sql` without replacing users or panel settings and creates `setup.lock` again.

An upgrade never creates another administrator. A fresh installation is also refused when the selected database already contains tables.

### Test the setup page

Open `setup_test.php` to walk through the setup form on an already installed panel. It checks the server, submitted settings and database connection, but does not write configuration files, change permissions or modify the database.

Remove `setup.php` and `setup_test.php` from the public server when you no longer need them. The setup lock prevents accidental installation changes in the meantime.

### Configuration

The installer writes all normal configuration to `app/core/LocalConfig.php`, which is kept outside Git and works reliably with Apache and nginx. Real server environment variables take priority over this file, which also makes the same installation suitable for a later Docker deployment.

Never commit database passwords, API keys, Discord secrets or webhook URLs.

When using nginx, keep `DB.sql` and `migration.sql` outside the public webroot when possible. Otherwise add this to the server block:

```nginx
location ~ /(?:DB\.sql|migration\.sql)$ {
    deny all;
    return 404;
}
```

## Features

### Authentication and security

- Optional invite-only registration
- Remember-login tokens with expiry and token management
- Optional Discord two-factor authentication
- Cloudflare Turnstile, hCaptcha, Google reCAPTCHA or disabled captcha
- CSRF protection and rate limiting for sensitive actions
- Ban and maintenance pages

### Users

- Subscription activation using generated codes
- Protected client download for subscribed users
- Public profiles, display names, short profile bios and Discord avatars
- Password changes and login/session management
- User notifications and activity logs
- Optional shoutbox with user muting

### Support and administration

- User, ban and invite management
- Subscription, invite and role gifts in their own navigation section
- Password and HWID resets
- Subscription freezing and invite waves
- Client upload and panel status/version management
- Shoutbox, Discord linking and Discord logging settings
- Configurable captcha provider and custom API variables
- Separate supporter permissions for common moderation tasks

## API

The available requests and short examples are documented in [API.md](API.md).

## Credits

- [@znixbtw](https://github.com/znixbtw/) for the original [php-panel-v2](https://github.com/znixbtw/php-panel-v2/).
- [@Phantom-1337](https://github.com/Phantom-1337/) and [@sxck1337](https://github.com/sxck1337/) for their themes.
