<?php
namespace Arbeitszeit\ExportModule;
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\i18n;
use Arbeitszeit\Exceptions;
use Arbeitszeit\Benutzer;
use Sujan\Exporter\Exporter;

/**
 * CSVExportModule - Allows you to export worktime sheets
 */

class CSVExportModule implements ExportModuleInterface
{

    public function export($args) {
        $arbeit = new Arbeitszeit();
        $year = $args["year"];
        $month = $args["month"];
        $user = $args["user"];

        $this->saveAsCsv($args);
        if (!is_string($year)) {
            $year = date("Y");
        }

        $sql = "SELECT id, username, schicht_tag, schicht_anfang, schicht_ende, ort, pause_start, pause_end 
                FROM `arbeitszeiten` 
                WHERE YEAR(schicht_tag) = ? AND MONTH(schicht_tag) = ? AND username = ? 
                ORDER BY schicht_tag DESC";
        $statement = $arbeit->db()->sendQuery($sql);
        $statement->execute([$year, $month, $user]);
        $data = $statement->fetchAll(\PDO::FETCH_ASSOC);

        // empty data
        if (empty($data)) {
            return false;
        }

        // Set headers and name
        $filename = "worktimes_" . $user . "_" . $year . "-" . $month . ".csv";
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Write to buffer
        $output = fopen('php://output', 'w');

        // Set columns
        $columns = ["ID", "Username", "Shift Date", "Shift Start", "Shift End", "Location/Notes", "Pause Start", "Pause End"];
        fputcsv($output, $columns, ';');

        // Datenzeilen in CSV schreiben
        foreach ($data as $row) {
            fputcsv($output, $row, ';');
        }

        fclose($output);
        exit;
    }

    public function saveAsCsv($args) {
        $arbeit = new Arbeitszeit();
        $year = $args["year"];
        $month = $args["month"];
        $user = $args["user"];
    
        if (!is_string($year)) {
            $year = date("Y");
        }
    
        $sql = "SELECT id, username, schicht_tag, schicht_anfang, schicht_ende, ort, pause_start, pause_end 
                FROM `arbeitszeiten` 
                WHERE YEAR(schicht_tag) = ? AND MONTH(schicht_tag) = ? AND username = ? 
                ORDER BY schicht_tag DESC";
        $statement = $arbeit->db()->sendQuery($sql);
        $statement->execute([$year, $month, $user]);
        $data = $statement->fetchAll(\PDO::FETCH_ASSOC);
    
        // Falls keine Daten vorhanden sind
        if (empty($data)) {
            return false;
        }
    
        // Erstelle den Dateipfad basierend auf den Argumenten
        $directory = $_SERVER["DOCUMENT_ROOT"] . "/data/exports/" . $this->getName() . "/$user";
        $filename = "$directory/worktimes_{$year}-{$month}.csv";
    
        // Verzeichnis erstellen, falls es nicht existiert
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
    
        // Datei öffnen
        $output = fopen($filename, 'w');
    
        // Setze die Spaltennamen in die CSV-Datei
        $columns = ["ID", "Username", "Shift Date", "Shift Start", "Shift End", "Location/Notes", "Pause Start", "Pause End"];
        fputcsv($output, $columns, ';');
    
        // Datenzeilen in CSV schreiben
        foreach ($data as $row) {
            fputcsv($output, $row, ';');
        }
    
        fclose($output);
    
        return $filename;
    }
    

    public function getName() {
        return "CSVExportModule";
    }

    public function getExtension() {
        return "csv";
    }

    public function getMimeType() {
        return "text/csv";
    }

    public function getVersion() {
        return "1.0";
    }

    public function geti18n() {}
    public function __set($name, $value)
    {
    }
    public function __get($name)
    {
    }
    public function __isset($name)
    {
    }
    public function __unset($name)
    {
    }
    public function __call($name, $arguments)
    {
    }
}

