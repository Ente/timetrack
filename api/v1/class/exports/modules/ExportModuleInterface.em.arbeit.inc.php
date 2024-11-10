<?php
namespace Arbeitszeit\ExportModule;

/**
 * Interface ExportModuleInterface
 * @package Arbeitszeit\ExportModule
 */
interface ExportModuleInterface {
    public function export($args);
    public function getName();
    public function getExtension();
    public function getMimeType();
    public function getVersion();
    public function geti18n();
    public function __set($name, $value);
    public function __get($name);
    public function __isset($name);
    public function __unset($name);
    public function __call($name, $arguments);
}
