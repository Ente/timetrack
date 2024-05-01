<!DOCTYPE html>
<html>
<?php
#ini_set("display_errors", 1);
require $_SERVER["DOCUMENT_ROOT"] .  "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;

$arbeit = new Arbeitszeit;
$base_url = Arbeitszeit::get_app_ini()["general"]["base_url"];
$ini = Arbeitszeit::get_app_ini();

$ds = $ini["dsgvo"];
?>

<head>
    <title>Datenschutzerklärung |
        <?php echo $ini["general"]["app_name"]; ?>
    </title>
    <link rel="stylesheet" type="text/css" href="/assets/css/index.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
</head>
<style>
    * {
        background-color: white !important;
        color: black !important;
        border-color: grey !important;
    }

    h2 {
        font-size: medium;
    }

    p {
        font-size: small;
    }

    hr {
        border-top: solid 1px #000 !important;
    }
</style>

<body>
    <h1>DSGVO |
        <?php echo $ini["general"]["app_name"]; ?>
    </h1>
    <div class="box" style="padding:20px;">
        <h2>Informationen zur Erfassung von personenbezogenen Daten</h2>
        <h3>1. Verantwortlicher</h3>
        <p><?php echo $ds["name"] ?></p>
        <p><?php echo $ds["street"] ?></p>
        <p><?php echo $ds["zipCity"] ?></p>
        <h2>2. Zweck der Datenverarbeitung</h2>
        <p>Wir verarbeiten personenbezogene Daten zu folgenden Zwecken: </p>
        <ul>
            <li>Erfassung und Verwaltung von Arbeitszeiten</li><br>
            <li>Erstellung von Schichtplänen</li><br>
            <li>Erstellung von Abrechnungen</li><br>
            <li>Erfüllung gesetzlicher Verpflichtungen, insbesondere in Bezug auf Arbeitszeiterfassung und
                Steuerangelegenheiten</li><br>
        </ul>
        <h2>3. Welche Daten werden erfasst</h2>
        <p>Für die oben genannten Zwecke erfassen wir folgende personenbezogene Daten: </p>
        <ul>
            <li>Vorname</li><br>
            <li>E-Mail-Adresse</li><br>
            <li>Uhrzeit, Beginn und Ende einer Schicht und ggf. Ort</li><br>
        </ul>
        <h2>4. Rechtsgrundlage für die Datenverarbeitung</h2>
        <p>Die Verarbeitung Ihrer Daten basiert auf der Rechtsgrundlage zur Erfüllung unserer vertraglichen
            Verpflichtungen Ihnen gegenüber.</p>
        <h2>5. Speicherdauer</h2>
        <p>Ihre Daten werden so lange gespeichert, wie dies für die oben genannten Zwecke erforderlich ist und wie es
            gesetzliche Aufbewahrungspflichten vorschreiben.</p>
        <h2>6. Empfänger der Daten</h2>
        <p>Ihre Daten können an folgende Empfänger weitergegeben werden: </p>
        <ul>
            <li>internes Personal</li><br>
            <li>externes Steuerbüro</li>
        </ul>
        <h2>7. Ihre Rechte</h2>
        <p>Sie haben das Recht auf: </p>
        <ul>
            <li>Zugang zu Ihren personenbezogenen Daten</li><br>

            <li>Berichtigung Ihrer personenbezogenen Daten</li><br>
            <li>Löschung Ihrer personenbezogenen Daten (per E-Mail an ***REMOVED***)</li><br>
            <li>Einschränkung der Verarbeitung Ihrer personenbezogenen Daten</li><br>
            <li>Datenübertragbarkeit</li><br>
            <li>Widerspruch gegen die Verarbeitung Ihrer personenbezogenen Daten</li><br>
        </ul>
        <p>Sie haben auch das Recht, eine Beschwerde bei einer Datenschutzbehörde einzureichen.</p>
        <p>Sie haben sich mit der Datenschutzvereinigung einverstanden erklärt, indem ihr Vorgesetzter o.Ä. Ihnen das <a
                href="IB.pdf">Informationsblatt</a> überreicht hat und Sie unterschrieben haben, </p>
        <p>die Datenschutzerklärung gelesen, verstanden und ihr zu zustimmen und einwilligen die Daten für die oben
            genannten Zwecke zu verarbeiten</p>

        <!--<p><span style="color:red">Hinweis: </span> Sie können das Informationsblatt herunterladen, indem Sie im obrigen Absatz auf den Link drücken.</p>-->

        <br><br>

        <p style="text-align:left"><i>Datum, Ort</i></p>
        <hr>
        <br><br>
        <p style="text-align:left"><i>Unterschrift Mitarbeiter</i></p>
        <br>
        <hr>
        <br><br>
        <p><i><span style="color:red">!!</span> Dieses Dokument unbedingt aufbewahren und eine Ausführung dem
                Mitarbeiter geben.</i></p>
    </div>
</body>

</html>