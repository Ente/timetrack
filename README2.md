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

Simply install the software by running the `installation.sh` with root inside the `setup` folder located in the projects root folder. After installation the folder should be removed entirely.
The script will move the required files to the destination folder, enables all the required PHP modules and installs the dependencies.

In step 2, you need to configure the `app.ini.sample` within the `api/v1/inc` folder:

- `language`: Either `de` or `en` which will specify the default language. This will get overwritten for the specific user if the browser sends something different than specified.
- `app_name`: The name of your application, e.g. `ACME Inc. TimeRecording`
- `base_url`: The Base URL (can also be an IP) of your application, without ending trailing slash and the protocol, e.g. `acme.inc` or `10.10.10.2`
- `support_email`: An email displayed to users in case of help, e.g. `support@acme.inc`
- `debug`: (deprecated)
- `auto_update`: (not yet implemented)
- `db_*`: Set the connection details for your mysql instance

### Requirements

- at least PHP 7.4
- Apache2.4 with enabled htaccess mod
- composer (to install dependencies)

This software has been tested on Debian 11 and 12.

### Upgrade from TimeTrack OSS to TimeTrack Oval

Simply check out our website: <https://timetrack.openducks.org/docs/upgrade-oss-to-oval>
You would need to register a new account and a complete dump of your SQL database
