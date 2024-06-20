# Toil API

The Toil API is a Application programming interface for TimeTrack.
Currently, there are 5 supported endpoints:

* getVersion - returns the current installed version.
* healtchcheck - returns an array "status" => "alive" in json.
* getUserCount - returns the current user count.
* getApiVersion - returns the current version of the Toil API.
* getLog - Returns the full log, requires additional authentication.

Aufrufe erfolgen über die Base URL "https://{domain}.{tld}/api/v1/toil/". Die Authentifizierung läuft hierbei über HTTP Basic.
Beispiel: "https://benutzer:meinpasswort@meine.domain/api/v1/toil/retrieveLatestVersion

Die API verwendet zur Authentifizierung Basic Auth. Der Benutzer benötigt zum Abfragen der API administrative Rechte.