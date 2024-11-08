# Toil API

The Toil API is a Application programming interface for TimeTrack.
Most endpoints are using `GET` method to either change, delete, view or add data.

* `getVersion` - Returns the current installed version. (user)
* `healtchcheck` - Returns an array "status" => "alive" in json. (user)
* `getUserCount` - Returns the current user count. (admin)
* `getApiVersion` - Returns the current version of the Toil API. (user)
* `getLog` - Returns the full log, requires additional authentication. (admin)
* `getUserDetails` - Returns the account details of an username provided (admin)
* `approveVacation` - Approves a vacation request. When using this method no email will be sent. (admin)
* `getVacations` - Get a JSON array of all vacation requests. (admin)
* `getWorktimes` - Get a JSON array of all worktime records. (admin)
* `getUsers` - Returns an JSON array with id -> username pairs (admin)
* `addWorktime` - Allows to add a worktime entry (admin)
* `addVacation` - Allows you to add a vacation entry for a specific user (admin)
* `getOwnWorktime` - Returns the worktime entries of the authenticated API user (user)
* `addOwnWorktime` - Allows you to add a worktime entry for the authenticated API user (user)
* `addOwnVacation` - Allows you to request vacation for the authenticated API user (user)
* `getUserWorktimes` - Returns a JSON array of all worktime entries of a specified user (admin)
* `addProject` - Allows you to add a project (admin)
* `addUser` - Create a new user (admin)
* `deleteUser` - Delete a desired user (admin)
* `getUserDetails` - Get a JSON array of the user's details (admin)
* `getVersion` - Returns the current installed TimeTrack version (user)

You can access the API via the base URL, e.g. `"https://{domain}.{tld}/api/v1/toil/"`. HTTP Basic is used for authentication.
Example: `https://{ADMIN_USERNAME}:{ADMIN_USER_PASSWORD}@{domain}.{tld}/api/v1/toil/getVersion`

You have to be authenticated to use the API.

## Custom Routes

Custom API routes can be added by adding the following to the `/data/routes/routes.json`, e.g.:

```json

{
    "myRoute": "/relative/path/to/myRoute.php"
}

```

If you then access `/api/v1/toil/myRoute` the `myRoute.php` will be returned. To be able to access your endpoint you also have to specify if it's for admins only (1) or for anyone authenticated (0) within the `permissions.json`
Your custom API route must implement the `EPInterface` (EndPoint) interface:

```php
<?php

namespace Toil;

interface EPInterface {
    public function get();
    public function post($post = null);
    public function put();
    public function delete();
    public function __construct();
    public function __set($name, $value);
    public function __get($name);
}
class EP extends Toil {

}

```
