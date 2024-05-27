# Toil API

Die Toil API ist eine Anwendungsprogrammierschnittstelle für das Arbeitszeiterfassungsprogramm.
Zurzeit werden 3 Endpunkte unterstützt:

* getVersion - gibt die aktuelle Version zurück.
* healtchcheck - Antwortet mit "alive" und plain.
* getUserCount - Gibt die Anzahl der Benutzer zurück.
* getApiVersion - Gibt git aktuelle Version der Toil API zurück.
* getLog - Gibt den tagesaktuellen Log zurück.

Aufrufe erfolgen über die Base URL "https://{domain}.{tld}/api/v1/toil/". Die Authentifizierung läuft hierbei über HTTP Basic.
Beispiel: "https://benutzer:meinpasswort@meine.domain/api/v1/toil/retrieveLatestVersion

Die API verwendet zur Authentifizierung Basic Auth. Der Benutzer benötigt zum Abfragen der API administrative Rechte.