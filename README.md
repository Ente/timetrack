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
You can create up to 30 Users, before you would have to upgrade to TimeTrack Oval

## Installation

Simply install the software by following these steps:

- Install php and requirements: `apt update && apt install php8.0 php8.0-curl apache2 mariadb-server -y` and enable the apache rewrite mod `a2enmod rewrite && service apache2 restart`
- Create a new database, e.g. with the name `ab` and create a dedicated user, e.g. `timetool`: `CREATE DATABASE ab;` and `CREATE USER 'timetool'@'localhost' IDENTIFIED BY 'yourpassword';` and `GRANT ALL PRIVILEGES ON ab.* TO 'timetool'@'localhost';`; don't forget to `FLUSH PRIVILEGES;`!
- Import the `setup/sql.sql` into your database, e.g. `mysql -u timetool -p ab < /full/path/to/sql.sql`
- To create your first user, run the `setup/usercreate.php` file, e.g. `php ./usercreate.php admin yourpassword email@admin.com` - `usercreate.php [USERNAME] [PASSWORD] [EMAIL]`
- Run the statement printed by the `usercreate.php` inside your database.

In step 2, you need to configure the `app.ini.sample` within the `api/v1/inc` folder:

- `language`: Either `de` or `en` which will specify the default language. This will get overwritten for the specific user if the browser sends something different than specified.
- `app_name`: The name of your application, e.g. `ACME Inc. TimeRecording`
- `base_url`: The Base URL (can also be an IP) of your application, without ending trailing slash and the protocol, e.g. `acme.inc` or `10.10.10.2`
- `support_email`: An email displayed to users in case of help, e.g. `support@acme.inc`
- `debug`: (deprecated)
- `auto_update`: (not yet implemented)
- `db_*`: Set the connection details for your mysql instance

If done correctly, you should now be able to access the application via http://BASE_URL/ - redirects to http://BASE_URL/suite/

**Please delete the whole `/setup/` folder after installation**

### Requirements

- at least PHP 7.4
- Apache2.4 with enabled htaccess mod
- composer (to install dependencies)

This software has been tested on Debian 11 and 12.

### Upgrade from TimeTrack OSS to TimeTrack Oval

Currently, this is not available, but we are working on an solution.
You would need to register a new account and a complete dump of your SQL database.

## Maintenance Mode

To enable the maintenance mode, simply rename the `api/inc/.MAINTENANCE` to `MAINTENANCE` (without the dot) to enable the functionality. No one will be able to access the application, aswell as administrators.
Disabling is done by renaming the file again.
