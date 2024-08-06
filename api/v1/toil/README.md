# Toil API

The Toil API is a Application programming interface for TimeTrack.
Currently, there are 5 supported endpoints:

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
* addWorktime - **Only Admins** | Specify the values within your GET request like this:
  date=2024-09-01&end=18%3A45&location=Berlin%20-%20Shop%201&meta=null&pause=%5Bobject%20Object%5D&start=11%3A30&username=admin

Which is the exact same just in JSON:

```json
{
  "start": "11:30",
  "end": "18:45",
  "date": "2024-09-01",
  "location": "Berlin - Shop 1",
  "username": "admin",
  "pause": {
    "start": "14:30",
    "end": "15:00"
  },
  "meta": "null"
}

```

You can access the API via the base URL, e.g. "https://{domain}.{tld}/api/v1/toil/". Authentication HTTP Basic is used.
Example: "https://benutzer:meinpasswort@meine.domain/api/v1/toil/getVersion

The authenticated user needs administrative rights to access the API.
