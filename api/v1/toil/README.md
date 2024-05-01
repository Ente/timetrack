# Toil API

Die Toil API ist eine Anwendungsprogrammierschnittstelle für das Arbeitszeiterfassungsprogramm.
Zurzeit werden 3 Endpunkte unterstützt:

* retrieveLatestVersion - Fragt und gibt die neuste Version zurück.
* getVersion - gibt die aktuelle Version zurück.
* healtchcheck - Antwortet mit "alive" und plain.
* getUserCount - Gibt die Anzahl der Benutzer zurück.
* getApiVersion - Gibt git aktuelle Version der Toil API zurück.
* getSlots - gibt die Anzahl des Kontigents der Benutzererstellung zurück.
* getLicenseInformation - gibt detallierte Informationen über die eingespielte Lizenz zurück.

Aufrufe erfolgen über die Base URL "https://{domain}.{tld}/api/v1/toil/". Die Authentifizierung läuft hierbei über HTTP Basic.
Beispiel: "https://benutzer:meinpasswort@meine.domain/api/v1/toil/retrieveLatestVersion

Die API verwendet zur Authentifizierung Basic Auth. Der Benutzer benötigt zum Abfragen der API administrative Rechte.