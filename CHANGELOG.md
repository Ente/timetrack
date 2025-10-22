# CHANGELOG

## v8.2.1

* Added events for worktime correction proposals: `WorktimeCorrectionProposed`
* Sanitized outputs to prevent XSS attacks
* Added a link to the documentation within the settings page

## v8.2

* Users are now able to propose corrections to worktimes when they have been marked as for "in review".
* Added project management. Take a look into the README.md for more information.
* Administrators can now change the theme globally within TimeTrack. See more within `README.md`.
* Added `theme_file` and `force_theme` keys to the `app.json` `[general]` section
* Users can choose their own theme, if `force_theme` is set to `false`

## v8.1

* Toil API release `1.13`:
  * Added `editUser` endpoint
  * Added `getOwnUser` endpoint
  * `healthcheck` endpoint now includes the server time (ISO-8601) and API version
* Enhanced `app.json` with the `mobile` section (enable/disable app, enable/disable API token generation within Settings, per-Client rate limit, enable/disable QR-code mobile pairing) - for future mobile app release
* Fixed translations for German and English
* Added example values to `LDAP` section within `app.json`
* The ID of the worktime is now being displayed within `Worktime records` and `All worktime records`. 
* Updated `README.md`
* **Update requires DB migration** (see `README.md` section `Database`)
* Added projects (read more within the `README.md`)
* Upgrading composer dependencies, please run `composer update`:
  * `simple-router`: `4.3.7.2` to `5.0.0.3`
* Added function to check if current user is admin
* Added function to redirect directly to the suite page if an error occurs
* Fixed an error causing infinite redirects to the 500 HTTP Page when the database is not available.

## v8.0.2

* Small fixes to the web UI
* Updated `README.md`

## v8.0.1

* Readded the `nfclogin` button to the login page
* Fixed failed require for `statusMessages.arbeit.inc.php`

## v8.0

* Reworked the whole web UI, some elements may have not been migrated yet.
* Plugin Hub now integrates the Navigation Bar

## v7.13.1.1

* Added colors to status messages
* Security fixes

## v7.13.1

* Fixed issues with LDAP authentication
* Fixed an issue with setting the status of vacations
* Fixed a php leak for the log file causing the settings page to crash when the log file is too large and php memory limit is too low

## v7.13

* You can now set different types of worktimes. You can specify your own ones in the `app/v1/inc/config/worktime_types.json` file. If none is set, like when using the easymode, mode `0` will be used. Added `Wtype` parameter to `WorktimeAdded` event.
* Toil API release `1.12`: added Bearer token authentication, fixed an issue with the `addOwnWorktime` and `addWorktime` endpoints.
* Fixed an issue allowing normal users to reset all PINs for the CodeClock plugin
* Fixed some typos in some of the error messages
* Reworked the way status messages are displayed and used. Implemented a new `StatusMessage` class to handle status messages which can also be used by plugins.

## v7.12.1

* Updated `README.md`
* Updated Plugin template
* You can now set the timezone within `app.json` (see `README.md` for more information)
* Fixed an issue with the generatedExport event
* Fixed db migrations for first user creation

## v7.12

* Added a simple Favicon
* TimeTrack and API version are now displayed in the settings menu
* Added Events which can be listened to by plugins (see `api/v1/class/events/README.md`) / Developers can now create their own events
* Mails can now be disabled by setting the `smtp` setting to `false` within the `smtp` section of the `app.json`
* Fixed `composer.json` contents for LDAPTools plugin again
* Removed unused `Hooks` plugin class file

## v7.11

* Added plugin to allow NFC PC/SC login (see `api/v1/class/plugins/plugins/nfclogin/README.md`)
* Added db migrations with phinx to update the database schema
* improved overall security with function node system
* API can now handle public endpoints

## v7.10.2

* Added native function to `Benutzer` class to update user proprties which not lets the `userdetail` plugin actually update user properties
* Fixed bug

## v7.10.1.1

* Hotfix preventing to add a worktime in normal mode

## v7.10.1

* Added some CSS to certain elements which were missing it

## v7.10

* Reflected changes from v7.9 release into Mails.md
* You now get redirected when calendar ID is not found
* Fixed being unable to access the "Forgot password" page
* Fixed typo in userdetail plugin preventing save
* Remove usercount plugin entirely
* Removed webedit for app.json
* A warning is now displayed when an admin changes user information within the userdetail plugin
* Fixed an bug causing userdetail plugin to crash when the selected user could not be found

## v7.9

* Fixed being unable to access "userdetail" plugin
* Fixed "debug" having not effect on Exceptions class
* Removed logrotate-cache.txt
* Sanitized various inputs like i18n files to prevent XSS attacks. Take a look into the `i18n.arbeit.inc.php` class for a ruleset and alternative "tags" to type (instead of `<br>` use `[BR]` within the snippets_*.json)
* Security improvements
* You can now download all your worktimes available offered by the ExportModules within the ExportManager plugin
* Admins can now download all worktimes available offered by the ExportModules within the ExportManager plugin.
  * To see a export in this menu, you need to create it first, e.g. via the "All Worktimes" page.
* Removed unused plugins classes
* Defined `MailTemplateData` class as requirement for `MailTemplate` class
* Removed `array` as return type for `MailTemplateInterface::render()`
* Improved autodetect for MailTemplates
* Other small improvements

<!-- Added phpdocs to Benutzer class -->

## v7.8

* Refactored the mails module. Read more about this within the `api/v1/class/mails/Mails.md`.
* Admins can now edit users via the GUI directly.
* Added a link to GitHub issues and to TimeTrack Roadmap within the settings.
* Updated `composer.json` to a `ldaptools` version that supports PHP 8.0. So the patch does not need to be applied anymore.
* Fixed `userdetail` plugin not creating the `data` directory.
* Removed the "Create Mailbox" checkbox when editing a user.
* Removed and deprecated most Plugin Phar functionality.
* Fixed `Exceptions::deprecated` function being not static.
* Rewritten some parts of the `README.md`
* Toil API release `1.10` added routes for the notifications module: `getNotifications`, `autoremoveNotifications`, `addNotification` and `removeNotification`.
* Fixed an issue being unable to export PDFs with the `PDFExportModule`.

<!-- Fixed an bug resulting in being unable to access the "forgot password" page -->
<!-- Fixed missing title within the "Add vacation" view -->
<!-- Added internal function to get all notifications -->

## v7.7.1

* Added function within the `Exceptions` class to show type "deprecated" warnings.

## v7.7

* Fixed duplicated active worktime entries by trying to fix it automatically.
* Fixed clocking in when multiple worktimes are active with QRclock plugin.
* Added `CustomRoutes::getCustomRoutes()` and `CustomRoutes::getCustomRoute($route)` functions to get all custom routes/one specific route file.
* **Added a plugin manager to manage all plugins.** This plugin is enabled by default and can be disabled within the `plugins.yml`. It allows you to enable/disable plugins.
* Fixed a bug while creating a new user.

<!-- Fixed missing API route for Codeclock plugin. -->
<!-- PluginBuilder is now able to return the plugin configuration in raw YAML -->
<!-- Adding CSS to certain pages -->
<!-- You can now dump all included files by setting "debug" to true within the app.ini -->
<!-- Added more logging messages-->

## v7.6

* **Replaced `app.ini` with `app.json`**. The `app.ini` has been deprecated and will be removed within the `8.0` release. Your settings will be automatically migrated to the new `app.json` file.
* Added plugin to clock in with QR codes. This plugin is disabled by default and can be enabled within the `plugins.yml`. More information can be found inside the `README.md`.
* You can now register or remove a custom API route via the `CustomRoutes::registerCustomRoute(...)` or `CustomRoutes::removeCustomRoute(...)` functions. More information can be found inside the Toil API `/api/v1/toil/README.md`.
* Added a plugin to clock in with a code. This plugin is disabled by default and can be enabled within the `plugins.yml`. More information can be found inside the `README.md`.
* Added a export manager plugin. This plugin is disabled by default and can be enabled within the `plugins.yml`.

## v7.5

* Added CSV export module class `Arbeitszeit\ExportModule\CSVExportModule` which can be used by clicking on `(CSV)` within All Worktimes
* Renamed all GUI elements from `Calendar` to `Notifications`
* Fixed being unable to edit notifications entries
* PDF and CSV exports are now directly saved onto the server. This is done automatically. Exports are saved within `data/exports/{ExportModuleName}/{username}/`
* CSS for PDF exports can now be customized. You can specify your own CSS file within the `app.ini` `[exports][pdf][css]` setting (full path)

<!-- Fixed title not set for Plugin Hub -->
<!-- Fixed missing i18n files for `suite/admin/notifications/edit.php` -->
<!-- Fixed being unable to approve or reject vacation entries -->

## v7.4

* Exports are now handled differently. You can import your own modules to support more file formats. Read more about this in the `README.md` file.
* Replaced `Arbeitszeit\PDF` with the new `Arbeitszeit\ExportModule\PDFExportModule` class
* Added Dutch (Nederlands) translation (locale `nl_NL`)

<!-- Renamed `Kalender` class to `Notifications` to make space for the name for future use -->
<!-- Updated docs for `Notifications` class' functions -->

## v7.3.1

* Fixed being unable to toggle easymode

<!-- The status row for both Sickness and Vacation reports is now translated -->

## v7.3

* Toil API release `v1.8` now supports loading custom routes. Read more about this feature in the `api/v1/toil/README.md` file.

<!-- Updated Toil README.md to reflect the changes done -->

## v7.2.2

* Completely removed the mailbox functionality which has been deprecated with `v6.5.1`
* Bug and additional fixes

<!-- Adjusted classes functions scopes to `private` where possible -->
<!-- Fixed an issue making it unable to initialize plugins correctly -->
<!-- Removed `help.php` file -->
<!-- Removed email notification for a deleted worktime entry -->

## v7.2.1

* Bug fixes and minor changes
* Updated Toil API to `1.7`: Added permission handling and added API endpoint `getOwnWorktimes`, `addOwnWorktime`, `addOwnVacation`

<!--
bug & additional fixes:

- removed version from Toil API response
- added `public` keyword to `loadLanguage()` function within i18n class
- updated Toil APIs `README.md`
- rewritten `getUserCount` API endpoint to match `EP` Interface of Toil
- made certain API endpoints only available for admins therefore added the `Permissions.routes.toil.arbeit.inc.php` class to manage 
- removed version hash from Toil API `VERSION` file
- fixed unset variable for i18n within the `Forgot Password` page
- fixed being unable to reset password
- fixed unset variable for easymode action
- fixed links
- fixed an bug soft-locking the dashboard if using easymode and the active worktime got deleted
- fixed an bug preventing to end worktimes if using the easymode
- fixed an bug preventing to delete worktime entries
- fixed not being able to add a vacation/sickness
- added status message if password reset mail has been sent

-->

## v7.1

* Added API routes for Toil API `1.6`: `addUser`, `deleteUser`, `deleteWorktime`, `getUserWorktimes` <!-- DONE -->
* Rewritten `README.md` and updated `composer.json` <!-- DONE -->

<!--

additional fixes:

- added `window.print()` to PDF output to trigger printing directly.
- cleaned up `suite/*` files
- added statistics to `projects.arbeit.inc.php`
- added 404 and 403 handling to Toil API
- fixed not being able to reject a vacation

-->

## v7.0

* The Toil API has been enhanced within its `1.5` release: `addVacation`, `getLog` (allowing to get a specific log now), `addProject` <!-- DONE -->
* Toil API has been reworked into a class: `toil.arbeit.inc.php` (and its subclasses `Controller.toil.arbeit.inc.php`, `API.toil.arbeit.inc.php`, `Routes.API.toil.arbeit.inc.php`, `*.ep.API.toil.arbeit.inc.php`) <!-- DONE -->
* Database communication has been outsourced from the `class/*`-Classes to the newly added `class/db/db.arbeit.inc.php` <!-- DONE -->
* A few bugs have been fixed.
* Removed unused code. <!-- DONE -->
* Moved code from `suite/*` files into the appropriate classes.

<!--
"bug" fixes:

- fixed some typos
- fixed use of undefined vars
- fixed incorrect "getWorktimes" endpoint URL
- fixed typos in API routes

- added `projects.arbeit.inc.php` for future release v8.0

-->

## v6.8

* Enhanced Toil API with its `v1.4` release: `addWorktime`, `approveVacation`, `getUserDetails`, `getUsers`, `getVacations`, `getWorktimes`
* (Internal) Added a function to return all worktimes to the `arbeitszeit.inc.php`
* Added LDAP authentication
* Added a database scheme updating class and utility to upgrade the database scheme after an software update

<!-- 
additional fixes:

- fixed incorrect title for the 403 error page
- in order for LDAP to work with ldaptools and php 8.0 an additional patch is required applied by cweagans/composer-patches
- added update and ldap documentation

-->

## v6.7.2

* Bug fixes
  
<!--
bug fixes:

- login() function now reports if the login was successfully or not
- fixed plugins not able to load cause of a hardcoded path
- fixed broken links for vacation and sickness reporting in the menu
- fixed an issue while logging in on certain older devices with the app.ini set to app=true, fixes [#2](https://github.com/Ente/timetrack/issues/2)

-->

## v6.7.1

* Bug fixes
* TimeTrack is now fully localized in German and English

<!--
bug fixes:

- fixed the `calendar/all.php` used is_admin function to first retrieve the user data
- fixed users not being able to login if there is a whitespace character [#1](https://github.com/Ente/timetrack/issues/1)
- Not a bug, but rewritten the error pages to english
- now returning the error message when login attempt failed
- localized all missing pages
- localized all emails and returned strings in classes
- pdf exports now open in an extra tab

-->

## v6.7

### Generally

* Added more translated pages in English
* Added composer install command to `README.md`
* You are now able to mark a sickness entry as either approved, rejected or pending

<!--
additional fixes:

- removed an debug error reporting from pluginbuilder class
- reworked `Benutzer::get_all_users()` function to return all users in an array instead of just one
- added steps to install composer requirements to `README.md`
- added a check to the `Auth::mail_init()` function to check that the recipent is set
- Fixed a typo inside the `MailPasswordReset`, `MailVacationApproved`, `Mode` classes
- Slashes now get translated to either a forward or backlash one depending on the used OS within the `PluginBuilder` class
- Removed `usercount` plugin and replaced with `userdetail`
- Fixed SQL within the `Vacation` class
- Reworked the `/suite/actions/auth/reset.php` file

-->

## v6.6

### Generally

* Programmers are now able to create own plugins for TimeTrack. Please read more in the documentation, located at `/api/v1/class/plugins/docs`

<!--
other changes

- removed unused class `language.inc.php`
- added a plugin system working with yaml files, see more within the documentation and the template plugin

-->

## v6.5.1

### Generally

* Bug fixes
<!--
the fixes:

- fixed incorrect form URL for the edit calendars form
- fixed the error that calendar entries do not get updated
- removed the actions to add/edit or remove a mailbox entry
- fixed more incorrect header uses

-->

## v6.5

### Generally

* Added localization for some pages in english
* Removed unused dependency (tcpdf)
* Removed non-existend API routes
* You are now able to remove the state `pending` from a vacation or sickness report to either `rejected` or `approved`
* Fixed the incorrect display of the navigation bar, whilest on the password reset page

### Settings

* Mail authentication allows the use of ssl now by setting `usessl` in the `app.ini` to `true`

<!--
other changes

* added more authentication and login evaluation into the code
* removed mailbox class dependency from vacation and sickness classes and used files
* fixed incorrect sql.sql file
* removed conflicting apache2 keyword "Satisfy" as it is deprecated as in v2.4
* fixed hard coded/relative paths
* fixed some errors within the Arbeitszeit\Vacation class
* fixed incorrect sql for the `usercreate.php` script
* fixed redirect for the `delete worktime`, `review worktime`, `unlock worktime` endpoint
* 

-->

## v6.4

### Generally

* Added the posibility to allow logins via a iFrame provided instance, see `README.md` for more information on this topic

## v6.3 <!-- bugfix version -->

### Generally

* Usability on mobile devices has been improved

## v6.2

### Generally

* **Vacations can now be recorded via working time recording.**

## v6.1

### Generally

* **Break times can now be recorded via working time recording.** This is also supported in Easymode <!-- rev 2 -->
* Some GUI elements were deprecated. These have now been renewed.

### PDF

* Fixed some errors in displaying break time <!-- rev 1 -->

## v6.0

### Generally

* **Added the ability to record illnesses and vacations.**
* It is now possible to view the working hours of all employees (Admin). <!-- rev 1 -->

### API

* There is now more logging.
* The API now returns the latest version without control characters.

<!--
Minor Changes
------------------
- updated Toil README rev 1
- fixed small bugs regarding the new sickness functionality rev 2
-->

## 5.3

## API

* A bug has been fixed so that it is now possible to create calendar entries. Note: Calendar entries that have expired are automatically deleted.

## 5.2

## Generally

* Fixed a bug that allowed saving calendar entries without a note.
* **Employees' break times are now recorded. This allows you to check whether break times are adhered to afterwards. The program does not currently check the shift time to calculate the corresponding break time. This will be added in the next version.
* An error when displaying an image has been fixed. <!-- rev 1 -->

## 5.1

### Generally

* Users now receive an email when work time is deleted. <!-- done, tested -->
* Small errors have been corrected. <!-- fixed calendar html title, removed unnecessary files-->
* The API can now return the number of users. <!-- done, tested -->

### Users

* How many remaining users are in the quota is now displayed on the "Edit Users" page.

## v5.0

### Generally

* The buttons have a new look, which now also fixes the bug that caused buttons to disappear behind text and were no longer clickable on mobile devices. <!-- done, tested -->
* **Working times can now be marked as "for review" by the employer. This will then be displayed in red in the list.** <!-- done -->
* **Preparations have been made for an update manager to be added to the Settings menu, which will only be visible to administrators.**
* **A new API called Toil has been added. Access is via "https://[domain].[tld]/api/v1/toil/[endpoint]". Further information about the API can be found in the directory `/api/v1/toil/README.md`** <!-- done, tested -->

### Settings

* **A second mode has been introduced that allows employees to record their working hours more easily. This can be activated in your own settings under "simplified mode".** <!--done, tested -->
* **Administrators now have the option of viewing a log.** <!-- added, completed, tested in all classes -->

### Users

* Fixed a bug that made it impossible to delete users. <!-- done, tested -->
* After deleting a user, they will now receive an email. <!-- done, tested -->

## v4.0

### Generally

* The application has been expanded to include a digital mailbox. <!-- done -->
* Emails will now be sent to new users. You can now reset your password via email. <!-- done -->
* A bug has been fixed that made login impossible due to incorrect logic. <!-- done -->
* A bug was fixed that made it possible to enter working times from the past. <!-- done -->
* A message will now be displayed if the login is incorrect. <!-- done -->

### Settings

* An SMTP category has been added to the configuration file. <!-- done -->

### Users

* Users can now be edited. <!-- done -->

### Calendar

* Modifying calendar entries is now reserved for administrators. <!-- done -->

## v3.0

### Generally

* The security of the application has been adjusted.

* Added the "app_name" setting in app.ini.

* There are now consistent error messages.

* It is now no longer possible to enter working times for the future.

* Fixed a bug when creating users.

* The initialization file is checked in advance for valid values.

* It is now checked whether the user accesses the page via the "base_url" entered in the configuration file.

* Fixed some links that caused incorrect redirects.

* A maintenance mode has been implemented.

* "Clean URLs" introduced.

* Some settings can now be changed via the GUI.

### Calendar

* Fixed a bug where the note was not reflected in the database.

### Users

* Administrative accounts can now be created.

### Security

* Encryption of passwords has been changed
  
* The "state" attribute is now checked for enhanced security

## v2.1

### Generally

* Fixed a bug that caused "All Working Hours" to appear twice in the navigation bar

* Fixed various redirect errors

* The "Action" was not displayed under the menu item "Edit User" due to missing source code. This has now been fixed [Bug 13]

* All users are now displayed under "Edit Users", previously there was only one. This has now been fixed

* The changes are now displayed under `"Settings" > "Changes"`

* The source code has been better documented

* **One month's working hours are now calculated** [Bug 9]

* Info messages are now displayed, e.g. when you enter a shift. [Bug 15]

### PDF

* Fixed bug with deleted users. No display name was returned and left blank. Now the last known one from working hours is used there [Bug 14]

### GUI

* Debug setting disabled which exposed variables

### Calendar

* It is now possible to delete calendar entries

* Calendar entries can now only be viewed when logged in (bug)

### Settings

* Instead of the employee's name, a variable was exposed. This has been fixed and now the name is displayed properly

## v2.0

### Generally

* Code rewritten to be object-oriented to increase performance and make maintenance easier

* A new folder structure has now been introduced: `/suite/*`

* Adjusted file paths for navigation bar. [Bug 12]

### Users & Authentication

* Users' passwords are now stored encrypted to increase security

* Multiple users can now have admin status

### Calendar

* Fixed a bug when editing a calendar entry where each entry was edited

* Fixed several bugs that caused the calendar to not display/incorrectly

### URL handling

* URLs are now controlled by a function to increase performance and make maintenance easier (WIP)

### PDF

* PDFs can now be created (as pre-printed form)

## v1.0

* Working work registration system (CHANGELOG.md has only now been introduced)
