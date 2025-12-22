<?php
/**
 ***********************************************************************************************
 * Configuration data for the Admidio plugin SudokuHelper
 *
 * @copyright The Admidio Team
 * @see https://www.admidio.org/
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2.0 only
 *
 ***********************************************************************************************
 */

global $gProfileFields;

$config_default['Plugininformationen']['version'] = '';
$config_default['Plugininformationen']['stand'] = '';
 
//Infos für Uninstall
$config_default['install']['access_role_id'] = 0;
$config_default['install']['menu_item_id'] = 0;

/*
 *  Mittels dieser Zeichenkombinationen werden Konfigurationsdaten, die zur Laufzeit als Array verwaltet werden,
 *  zu einem String zusammengefasst und in der Admidiodatenbank gespeichert. 
 *  Muessen die vorgegebenen Zeichenkombinationen (#_# und #!#) jedoch ebenfalls, z.B. in der Beschreibung 
 *  einer Konfiguration, verwendet werden, so kann das Plugin gespeicherte Konfigurationsdaten 
 *  nicht mehr richtig einlesen. In diesem Fall sind die vorgegebenen Zeichenkombination abzuaendern (z.B. in !-!)
 *  
 *  Achtung: Vor einer Aenderung muss eine Deinstallation durchgefuehrt werden!
 *  Bereits gespeicherte Werte in der Datenbank koennen nach einer Aenderung nicht mehr eingelesen werden!
 */
$dbtoken  = '#_#';  
