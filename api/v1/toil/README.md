# Toil API

The Toil API is a Application programming interface for TimeTrack.
Most endpoints are using `GET` method to either change, delete, view or add data.

* getVersion - returns the current installed version.
* healtchcheck - returns an array "status" => "alive" in json.
* getUserCount - returns the current user count.
* getApiVersion - returns the current version of the Toil API.
* getLog - Returns the full log, requires additional authentication.
* getUserDetails - Returns the account details of an username provided
* approveVacation - Approves a vacation request. When using this method no email will be sent.
* getVacations - Get a JSON array of all vacation requests.
* getWorktimes - Get a JSON array of all worktime records.
* getUsers - Returns an JSON array with id -> username pairs
* addWorktime - Allows to add a worktime entry
* addVacation - Allows you to add a vacation entry for a specific user
* getOwnWorktime - Returns the worktime entries of the authenticated API user
* addOwnWorktime - Allows you to add a worktime entry for the authenticated API user
* addOwnVacation - Allows you to request vacation for the authenticated API user
* getUserWorktimes - Returns a JSON array of all worktime entries of a specified user

You can access the API via the base URL, e.g. "https://{domain}.{tld}/api/v1/toil/". Authentication HTTP Basic is used.
Example: `https://{ADMIN_USERNAME}:{ADMIN_USER_PASSWORD}@{domain}.{tld}/api/v1/toil/getVersion`

The authenticated user needs administrative rights to access the API.
