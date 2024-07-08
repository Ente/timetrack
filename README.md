# TimeTrack - small enterprise time recording

TimeTrack aims to be a easy-to-use time recording software for small enterprises.
It's a fork from TimeTrack Oval, v6.2 (license-based model, within cloud & more features)

## Features

- Time recording for your employees (as well as Vacation and Sickness reporting)
- Email notifications
- Platform-wide calendar
- API-Integration (TimeTrack Oval)
- Logging
- English and German supported
- Maintenance mode
- Easy and fast installation

That's not even all of it, you can also generate timesheets (PDF) to export, user creation menu, an "easymode" to make it even easier to track your time and a mobile-friendly UI.
Additional functionality can be unlocked with TimeTrack Oval

## Installation

Simply install the software by following these steps:

- Install php and requirements: `apt update && apt install php8.0 php8.0-curl apache2 mariadb-server -y` and enable the apache rewrite mod `a2enmod rewrite && service apache2 restart`
- Install requirements for composer `cd /path/to/timetrack && composer install`
- Create a new database, e.g. with the name `ab` and create a dedicated user, e.g. `timetool`: `CREATE DATABASE ab;` and `CREATE USER 'timetool'@'localhost' IDENTIFIED BY 'yourpassword';` and `GRANT ALL PRIVILEGES ON ab.* TO 'timetool'@'localhost';` don't forget to `FLUSH PRIVILEGES;`!
- Import the `setup/sql.sql` into your database, e.g. `mysql -u timetool -p ab < /full/path/to/sql.sql`
- To create your first user, run the `setup/usercreate.php` file, e.g. `php ./usercreate.php admin yourpassword email@admin.com` - `usercreate.php [USERNAME] [PASSWORD] [EMAIL]`
- Run the statement printed by the `usercreate.php` inside your database.

In step 2, you need to configure the `app.ini.sample` within the `api/v1/inc` folder:

- `app_name`: The name of your application, e.g. `ACME Inc. TimeRecording`
- `base_url`: The Base URL (can also be an IP) of your application, without ending trailing slash and the protocol, e.g. `acme.inc` or `10.10.10.2` (URLs will be built with the http:// protocol, we recommend adding a redirect to https:// if you use an certificate.)
- `support_email`: An email displayed to users in case of help, e.g. `support@acme.inc`
- `debug`: (deprecated)
- `auto_update`: (not yet implemented)
- `db_*`: Set the connection details for your mysql instance
- `app`: If set to true, users will be able to use the TimeTrack mobile application

**SMTP section**

- `host`: FQDN of your mail server
- `username`: Username for the mailbox you want to send emails from
- `password`: Self explaining
- `port`: Specify a custom port or change the port if you do not want to use encryption
- `usessl`: Specify if you want to use STARTTLS (false) after initial communication or use SSL (true)

If you plan to use this system with a Gmail-Account, please be aware that you are not able to use your usual password. You would have to create a seperate `App Password`, you should note down.
You can do this following this link: <https://myaccount.google.com/u/0/apppasswords> or by navigating from <https://accounts.google.com> to `Security` > `2-Factor Authentication` > `App Passwords`. If you do not see this option on screen, use the link.

**Plugins** (Read more at `/api/v1/classes/plugins/docs`)

- `plugins`: Specify if you want to enable or disable plugins (default: true)
- `path`: Plugins path (default: `/api/v1/classes/plugins/plugins`)
- `data`: Data path for plugins, relative from the `path` variable (default: `data`)
- `testing`: Specify if the testing mode is enabled or not. If enabled, plugins which are not bundled within an phar archive are working aswell (e.g. just the source code within the plugins folder).

If done correctly, you should now be able to access the application via http://BASE_URL/ - redirects to http://BASE_URL/suite/

**Please delete the whole `/setup/` folder after installation**

After configuring, please rename the `app.ini.sample` to `app.ini` (`mv app.ini.sample app.ini`)

### Requirements

- at least PHP 8.0 (intl, mysqli, curl, fileinfo, ldap, sockets extension)
- Apache2.4 with enabled htaccess, headers mod
- composer (to install dependencies; phpmailer: for sending emails via smtp, parsedown: markdown parser for the `CHANGELOG.md`, simple-router: does the API routing)

This software has been tested on Debian 11/12 and XAMPP.

### Upgrade from TimeTrack OSS to TimeTrack Oval

Currently, this is not available, but we are working on an solution.
You would need to register a new account and upload a complete dump of your SQL database.

## Maintenance Mode

To enable the maintenance mode, simply rename the `api/inc/.MAINTENANCE` to `MAINTENANCE` (without the dot) to enable the functionality. No one will be able to access the application, aswell as administrators.
Disabling is done by renaming the file again.

## Permissions

TimeTrack only differenciates between two user groups:

- `Admin`: This group allows to change application settings, add calendar entries, manage users, manage worktime, sickness and vacation reports
- `User`: If in this group, you only have access to the elemental functions, like viewing calendar entries, add worktime/vacation/sickness

## Logging

Logs can be found inside the `./data/logs` path, they are named in the following scheme: log-{YEAR}-{MONTH}-{DAY}.log. Log files created do not get deleted automatically.
Another useful source, while expieriencing errors is the `/var/log/apache2/errors.log` file, containing the errors created by PHP.

## Language

TimeTrack supports German and English. Users currently can't actively switch between any of them, instead TimeTrack uses the locale provided by the browser.

## iFrame

For TimeTrack to work within iFrames, it is required to set the `samesite` parameter to `None`, `secure` to `true` and `domain` to your base url.
TimeTrack does this automatically for you, but we also recommend setting the `php.ini` attribute `session.cookie_samesite` according to PHPs documentation (e.g. `"None"` (don't forget the quotation marks)).
Please also enable the `headers` mod in apache2 (e.g. `a2enmod headers`)
