# Arbeitszeiterfassungssystem

In dieser Datei stehen wichtige Anmerkungen oder Knowledge-Artikel, die es sich Wert sind durchzulesen, sollte man auf Fehler treffen oder technische Fragen haben.

## Installation

Das Programm muss auf einem Linux-Server (bestenfalls Debian) mit PHP 7.4 und mysql ausgestattet sein. 
Im Verzeichnis `setup` befindet sich die InstallationsBash `installation.sh`, diese gilt es auszuführen und zu folgen. Rest passiert automatisch

### Migration

im Ordner `setup` befinden sich jeweils Migrations Dateien benannt in von -> zu Version, diese gilt es nach jedem Update auszuführen. Die Software überprüft das erfolgreiche Update nicht selber, daher ist das selbstständige ausführen des Skripts erforderlich.

## Dokumentation-Backend [uninteressant für Endnutzer]

### `get_calendar_entry()`-Funktion

Die Funktion gibt ein Array zurück. Unten steht beschrieben, wie die Daten innerhalb des Arrays gelesen werden können.

- `$data["ort"]` Beinhaltet die Straße, etc. des Kalendereintrags
- `$data["uhrzeit"]` Beinhaltet die Uhrzeit, zum Beginn des Kalendereintrags
- `$data["notiz"]` Die Notiz beinhaltet in der Regel die Aufgabe des Kalendereintrags
- `$datum` - *deprecated*
- `$data["datum"]` - Beinhaltet den UNIX-Timestamp
- `$data["datum_new"]` - Beinhaltet das "deutsche" Format für das Datum (TT.MM.JJJJ)

## Wartungen

Es gibt seit v3.0 die Möglichkeit einen Wartungsmodus zu aktivieren. Aktiviert wird der Modus, indem eine Datei namens "MAINTENANCE" in das "api/inc"-Verzeichnis erstellt werden (oder den Punkt aus der vorhandenen Datei entfernen ;)).

Dies deaktiviert jeglichen Zugriff auf die Seite und triggert das Umleiten auf eine 503 HTTP-Fehlerseite.
Zum Deaktivieren des Modus genügt es, die Datei wieder zu löschen. Zugriffe werden damit umgehend erlaubt.


## Logs

Seit v5.0 gibt es die Möglichkeit Logs einzusehen, diese liegen unter `/data/logs/log-{date}.log`.
Die Logs rotieren einmal pro Tag. Alternativ können die Logs über einen administrativen Account unter "Einstellungen" > "Logs" eingesehen werden.

## Email-Benachrichtigungen & Mailbox

v4.0 füge eine Mailbox und damit verbundene Email-Benachrichtigungen für neue Entries oder Benutzer, etc.
Die app.ini wurde entsprechend um eine [smtp]-Kategorie erweitert.

Dieses Feature benötigt einen Mailserver, welcher über Port 587 STARTTLS-Verschlüsselung durchführt.
In neueren Versionen, soll dieses Verhalten anpassbar sein.
Folgende Werte werden für die Einrichtung benötigt:

- `host` - Der Hostname des Mailservers (e.g. mail.domain.com).
- `username` - Der Benutzername deines Email-Postfaches (e.g. service / service@domain.com).
- `password` - Dein Passwort für das Postfach.
- `port` - Der Port, auf welchem der Mailserver für STARTTLS lauscht.

## Lizenzierung

Seit v6.0 wird das ASZE in verschiedenen Versionen angeboten. Insgesamt gibt es 3, wovon 2 kostenpflichtig sind.

- `S` - `S` bietet Platz für 10 Benutzer.
- `M` - Das `M`-Package ermöglicht bis zu 25 Benutzer.
- `L` - Das `L`-Package bietet dem Administrator Slots für insgesamt 50 Benutzer. Beim Überschreiten der 50 Benutzer, verlangt das ASZE ein Upgrade, welches dann nur noch mit einer `CM`-Lizenz möchglich ist.
- `CM` - Dieses Package ist exklusiv für Administratoren, welche mehr als 50 Benutzer benötigen. Da das System nicht ausgelegt ist, normalerweise mehr als 50 Benutzer zu pflegen, wird hier eine Segmentierung der Instanzen (in z.B. Abteilungen oder Standorte) empfohlen. Diese Lizenz kann auf mehreren Maschinen verwendet werden.
