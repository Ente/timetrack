<?php
require dirname(__DIR__, 1) . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Kalender;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Auth;
@$username = $_SESSION["username"];
$auth = new Auth;
$user = new Benutzer;
$arbeit = new Arbeitszeit;
$base_url = Arbeitszeit::get_app_ini()["general"]["base_url"];
$ini = Arbeitszeit::get_app_ini();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Hilfe-Menü | <?php echo $ini["general"]["app_name"]; ?></title>
        <link rel="stylesheet" type="text/css" href="/assets/css/index.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <h1>Hilfe-Menü</h1>
        <div class="box">
            <h2>Wie füge ich Arbeitszeiten hinzu?</h2>
                <p>Das Hinzufügen von Arbeitszeiten erfolgt über das "Hauptmenü"/"Menü". Dabei werden folgende Werte eingetragen:</p>

                <ul>
                    <li>Ort: Adresse oder Arbeitsbezeichnung</li>
                    <li>Datum: Das Datum, an dem die Arbeitszeit verrichtet wurde.</li>
                    <li>Schicht Beginn/Ende: Beginn bzw. Ende der Schicht</li>
                </ul>
            <hr>
            <h2>Wie entferne/modifiziere ich eine Arbeitszeit?</h2>
                <p>Zurzeit ist es noch nicht möglich, die eingetragene Arbeitszeiten zu modifizieren oder zu löschen. Bitte kontaktiere dafür deinen Administratoren (Unter Einstellungen > Support findest du deinen Ansprechpartner)</p>
                <hr>
            <h2>Was bedeuten die einzelnen Menüs?</h2>
                <p>Menü: Auch "Hauptmenü"; Dort kannst du deine Arbeitszeiten eintragen.</p>
                <p>Einstellungen: Erlaubt es dem Benutzer einige seiner Daten einzusehen und die neusten Änderungen des ASZEs zu sehen.</p>
                <p>Eigene Arbeitszeiten: Hier kannst du die von dir eingetragenen Arbeitszeiten einsehen.</p>
                <p>Eigene Mailbox: Hier werden/können dir Mitarbeitermitteilungen zugesendet. <span style="color:red;">Hinweis: Bitte schaue regelmäßig nach deinem Postfach, da zurzeit noch keine EMail-Benachrichtigungen erfolgen!</span></p>
                <p>Kalender*: Hier kannst du Kalendereinträge hinzufügen.</p>
                <p>Alle Arbeitszeiten*: Dort werden alle Arbeitszeiten von allen Angestellten aufgelistet. Hier können auch Stundenzettel für die Weitergabe an ein Steuerbüro generiert werden.</p>
                <p>Benutzer bearbeiten*: In diesem Menü können Benutzer erstellt, gelöscht und bearbeitet werden.</p>
                <p>Mailbox-Admin*: Hier können Mailbox-Einträge für Mitarbeiter erstellt werden oder auch automatisiert.</p>

                <b>Menüs die am Ende ein Stern (*) haben, sind Administratoren vorbehalten.</b>
                <hr>
            <h2>(Arbeitgeber) Ist es möglich die Lohnabrechnungen an die Mailbox zu verteilen?</h2>
                <p>Ja, das Mailbox-System ist flexibel und erlaubt als Anhänge Dokumente (PDFs, docx, odf), Bilder (z.B. JPEG, PNG) oder auch Links.</p>
                <p>Wenn Sie vertrauliche Dokumente an die Mailbox eines Mitarbeiters schicken, prüfen Sie vorher folgendes:</p>
                    <ul>
                        <li>Ist das Dokument für den Mitarbeiter bestimmt?</li>
                        <li>Sind alle Daten korrekt?</li>
                        <li>Ist das Dokument verschlüsselt? (Dies wird zurzeit noch nicht vom ASZE unterstützt)</li>
                    </ul>

                <p>Bei externen Inhalten gilt folgendes zu Beachten:</p>
                    <ul>
                        <li>Ist der Mitarbeiter über den Datenschutz des Drittanbieters informiert? (ggf. wird eine zusätzliche Bestätigung benötigt)</li>
                        <li>Ist der Drittanbieter vertrauenswürdig? (sichere Webseite "https", Impressum vorhanden?)</li>
                    </ul>
                <p>Das Mailbox-System erlaubt einen das nachträgliche Ändern eines Mailbox-Eintrages nicht. Sie können den Eintrag lediglich löschen.</p>
            <h2>(Arbeitgeber) Ich möchte die Arbeitszeiten stationär verwenden, ist das möglich?</h2>
                <p>Zurzeit arbeiten wir an einen dualen Modus für stationäre Geräte. Die Funktion wird in Version 5.0 (Q1 2024) hinzugefügt. Administratoren werden in einer seperaten E-Mail informiert.</p>
        </div>
    </body>
</html>