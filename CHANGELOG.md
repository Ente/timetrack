# CHANGELOG

## v6.3 <!-- bugfix version -->

### Allgemein

* Es wurden einige Fehler in der Lizenzierung behoben
* Die Benutzerfreundlichkeit auf mobilen Endgeräten wurde verbessert

## v6.2

### Allgemein

* **Urlaube können nun über die Arbeitszeiterfassung erfasst werden.**

## v6.1

### Allgemein

* **Pausenzeiten können nun über die Arbeitszeiterfassung erfasst werden.** Dies wird auch im Easymode unterstützt <!-- rev 2 -->
* Einige GUI-Elemente waren veraltet. Diese wurden nun erneuert.

### PDF

* Einige Fehler bei der Anzeige der Pausenzeit wurden behoben <!-- rev 1 -->

## v6.0

### Allgemein

* **Es wurde die Möglichkeit hinzugefügt, Krankheiten und Urlaube zu erfassen.**
* Es ist nun Möglich, die Arbeitszeiten aller Angestellten einzusehen (Admin). <!-- rev 1 -->

### API

* Es wird nun mehr geloggt.
* Die API gibt die aktuellste Version nun ohne Steuerzeichen zurück.

<!--
Minor Changes
------------------
- updated Toil README rev 1
- fixed small bugs regarding the new sickness functionality rev 2
-->

## 5.3

## API

* Es wurde der Endpunkt `getLicenseInformation` hinzugefügt, um detaillierte Informationen über die Lizenz zu erhalten. <!-- #23 -->
* Ein Fehler wurde behoben, sodass nun die Erstellung von Kalendereinträgen möglich ist. Hinweis: Kalendereinträge die "Abgelaufen" sind, werden automatisch gelöscht.

## 5.2

## Allgemein

* Es ist nun möglich, den Lizenzschlüssel anzupassen.
* Es wurde ein Fehler behoben, der es ermöglichte, Kalendereinträge ohne Notiz zu speichern.
* **Es werden nun die Pausenzeiten der Angestellten aufgenommen. Dies ermöglicht im Nachhinein die Kontrolle über die Einhaltung der Pausenzeiten. Das Programm prüft derzeit nicht die Schichtzeit um daraus die entsprechende Pausenzeit zu berechnen. Dies wird in kommenden Version ergänzt.
* Ein Fehler bei der Anzeige eines Bildes wurde behoben. <!-- rev 1 -->
* Es wurde ein Anzeigefehler bei der Anzeige des Lizenzschlüssels behoben. <!-- rev 1 -->

## 5.1

### Allgemein

* Benutzer erhalten nun eine E-Mail, wenn die Arbeitszeit gelöscht wird. <!-- erledigt, tested -->
* Es wurden kleine Fehler ausgebessert. <!-- fixed calendar html title, removed unnecessary files-->
* Die API kann nun die Anzahl der Benutzer zurückgeben. <!-- erledigt, getestet -->

### Benutzer

* Es wird nun angezeigt, wie viele verbleibende Benutzer im Kontigent vorhanden sind auf der "Benutzer bearbeiten" Seite.

## v5.0

### Allgemein

* Die Buttons erhalten ein neues Aussehen, was nun auch den Fehler behebt, das Buttons hinter Text verschwinden und nicht mehr klickbar waren auf mobilen Geräten. <!-- erledigt, tested -->
* **Arbeitszeiten können nun als "zur Prüfung" markiert werden vom Arbeitgeber. Dieser wird dann rot in der Liste angezeigt.** <!-- erledigt -->
* **Es wurden Vorbereitungen für ein Update-Manager, der im Einstellungsmenü hinzugefügt wird, welcher ausschließlich für Administratoren sichtbar ist.**
* **Eine neue API, namens Toil, wurde hinzugefügt. Der Zugriff erfolgt über "https://[domain].[tld]/api/v1/toil/[endpoint]". Weitere Informationen zu der API gibt es im Verzeichnis `/api/v1/toil/README.md`** <!-- erledigt, tested -->

### Einstellungen

* **Es wurde ein zweiter Modus eingeführt, welcher es Mitarbeitern erlaubt, ihre Arbeitszeiten einfacher zu erfassen. Dieser kann in den eigenen Einstellungen unter dem Punkt "vereinfachter Modus" aktiviert werden.** <!--erledigt, tested -->
* **Es gibt nun für Administratoren die Möglichkeit, einen Log einzusehen.** <!-- in allen Klassen hinzugefügt, erledigt, tested -->

### Benutzer

* Es wurde ein Bug behoben, der es unmöglich machte, Benutzer zu löschen. <!-- erledigt, tested -->
* Nach Löschung eines Benutzers, erhält dieser nun eine E-Mail. <!-- erledigt, tested -->

## v4.0

### Allgemein

* Die Anwendung wurde um ein digitales Postfach erweitert. <!-- erledigt -->
* Es werden nun E-Mails an neue Benutzer verschickt. Zudem kann man nun sein Passwort per E-Mail zurücksetzen. <!-- erledigt -->
* Es wurde ein Bug behoben, welcher den Login unmöglich machte aufgrund fehlerhafter Logik. <!-- erledigt -->
* Es wurde ein Bug behoben, der es ermöglichte Arbeitszeiten aus der Vergangenheit einzutragen. <!-- erledigt -->
* Es wird einem nun eine Meldung bei fehlerhaftem Login angezeigt. <!-- erledigt -->

### Einstellungen

* Die Konfigurationsdatei wurde um eine SMTP-Kategorie erweitert. <!-- erledigt -->

### Benutzer

* Benutzer können nun bearbeitet werden. <!-- erledigt -->

### Kalender

* Das Modifizieren von Kalendereinträgen ist nun Administratoren vorbehalten. <!-- erledigt -->

## v3.0

### Allgemein

* Die Sicherheit der Anwendung wurde angepasst.

* Es wurde die Einstellung "app_name" in der app.ini hinzugefügt.

* Es gibt nun einheitliche Fehlermeldungen.

* Es ist nun nicht mehr möglich, Arbeitszeiten für die Zukunft einzutragen.

* Ein Fehler beim Erstellen von Benutzern wurde behoben.

* Die Initalisierungsdatei wird vorab nach gültigen Werten überprüft.

* Es wird nun überprüft, ob der Nutzer über die "base_url", eingetragen in der Konfigurationsdatei, auf die Seite zugreift.

* Es wurden einige Links korrigiert, die für eine falsche Weiterleitung sorgten.

* Ein Wartungsmodus wurde implementiert.

* "Clean URLs" eingeführt.

* Einige Einstellungen können nun über die GUI geändert werden.

### Kalendar

* Es wurde ein Fehler behoben, bei dem die Notiz nicht in die Datenbank übernommen wurde.

### Benutzer

* Es können nun administrative Accounts erstellt werden.

### Sicherheit

* Die Verschlüsselung von Passwörtern wurde geändert
  
* Zur erweiterten Sicherheit wird nun das Attribut "state" überprüft

## v2.1

### Allgemein

* Fehler behoben, wodurch zweimal "Alle Arbeitszeiten" in der Navigationsleiste angezeigt wurde

* Diverse Fehler bei Weiterleitungen behoben

* Unter dem Menüpunkt "Benutzer bearbeiten" wurde die "Aktion" nicht angezeigt, aufgrund fehlenden Quellcodes. Dies wurde nun behoben [Bug 13]

* Auch werden unter "Benutzer bearbeiten" nun alle Benutzer angezeigt, vorher stand da nur einer. Dies wurde nun behoben

* Es werden nun die Änderungen angezeigt, unter `"Einstellungen" > "Änderungen"`

* Der Quellcode wurde besser dokumentiert

* **Die Arbeitszeit eines Monats wird nun berechnet** [Bug 9]

* Info-Nachrichten werden nun angezeigt, z.B. wenn man eine Schicht einträgt. [Bug 15]

### PDF

* Fehler behoben, bei gelöschten Nutzern. Es wurde kein Anzeigename zurückgegeben und leergelassen. Jetzt wird dort der zuletzt bekannte aus der Arbeitszeit verwendet [Bug 14]

### GUI

* Debug-Einstellung deaktiviert, welche Variablen freigelegt hat

### Kalender

* Es ist nun möglich Kalendereinträge zu löschen

* Kalendereinträge können jetzt nur noch angesehen werden, wenn man angemeldet ist (bug)

### Einstellungen

* Anstatt des Namen des Mitarbeiters, wurde eine Variable exposed. Dies wurde behoben und nun wird der Name ordentlich angezeigt

## v2.0

### Allgemein

* Code objekt-orientiert umgeschrieben um die Performance zu steigern und die Wartung zu erleichtern

* Es wurde nun eine neue Ordnerstruktur eingeführt: `/suite/*`

* Dateipfade für Navigations*Leiste angepasst. [Bug 12]

### Benutzer & Authentifizierung

* Die Passwörter der Benutzer werden jetzt verschlüsselt gespeichert um die Sicherheit zu erhöhen

* Es können nun mehrere Benutzer den Admin-Status besitzen

### Kalender

* Es wurde ein Fehler beim Bearbeiten eines Kalendereintrags behoben, bei der jeder Eintrag bearbeitet wurde

* Mehrere Fehler behoben, wodurch der Kalender nicht/fehlerhaft angezeigt wurde

### URL Handling

* URLs werden nun durch eine Funktion gesteuert um die Performance zu steigern und die Wartung zu erleichtern (WIP)

### PDF

* Es können nun PDFs erstellt werden (als Vordruck)

## v1.0

* Funktionierendes Arbeitserfassungssystem (CHANGELOG.md wurde erst jetzt eingeführt)
