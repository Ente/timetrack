# CHANGELOG

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
