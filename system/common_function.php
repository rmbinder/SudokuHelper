<?php
/**
 ***********************************************************************************************
 * Gemeinsame Funktionen fuer das Admidio-Plugin SudokuHelper
 *
 * @copyright rmb
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2.0 only
 ***********************************************************************************************
 */
use Admidio\Infrastructure\Utils\SecurityUtils;

if (basename($_SERVER['SCRIPT_FILENAME']) === 'common_function.php') {
    exit('This page may not be called directly!');
}

require_once (__DIR__ . '/../../../system/common.php');

$folders = explode('/', $_SERVER['SCRIPT_FILENAME']);
while (array_search(substr(FOLDER_PLUGINS, 1), $folders)) {
    array_shift($folders);
}
array_shift($folders);

if (! defined('PLUGIN_FOLDER')) {
    define('PLUGIN_FOLDER', '/' . $folders[0]);
}
unset($folders);

spl_autoload_register('myAutoloader');

/**
 * Mein Autoloader
 * Script aus dem Netz
 * https://www.marcosimbuerger.ch/tech-blog/php-autoloader.html
 *
 * @param string $className
 *            Die übergebene Klasse
 * @return string Der überprüfte Klassenname
 */
function myAutoloader($className)
{
    // Projekt spezifischer Namespace-Prefix.
    $prefix = 'Plugins\\';

    // Base-Directory für den Namespace-Prefix.
    $baseDir = __DIR__ . '/../../';

    // Check, ob die Klasse den Namespace-Prefix verwendet.
    $len = strlen($prefix);

    if (strncmp($prefix, $className, $len) !== 0) {
        // Wenn der Namespace-Prefix nicht verwendet wird, wird abgebrochen.
        return;
    }
    // Den relativen Klassennamen ermitteln.
    $relativeClassName = substr($className, $len);

    // Den Namespace-Präfix mit dem Base-Directory ergänzen,
    // Namespace-Trennzeichen durch Verzeichnis-Trennzeichen im relativen Klassennamen ersetzen,
    // .php anhängen.
    $file = $baseDir . str_replace('\\', '/', $relativeClassName) . '.php';
    // Pfad zur Klassen-Datei zurückgeben.
    if (file_exists($file)) {
        require $file;
    }
}

/**
 * Funktion erzeugt einen Button mit Link
 *
 * @param string $row
 *            Zeile des Buttons
 * @param string $col
 *            Spalte des Buttons
 * @param string $addColor
 *            html-Code für Hervorhebung eines Buttons
 * @return string html-Code mit Link für einen Button
 */
function generate_button($row, $col, $addColor = '')
{
    $ret = '';
    $text = '';

    if ($_SESSION['pSudokuHelper']['sudoku'][$row][$col]['set'] == 0) {
        $dist = '';
        $anz = 0;
        $fontsize = '8px';

        foreach ($_SESSION['pSudokuHelper']['sudoku'][$row][$col]['possible'] as $key => $data) {
            if ($data) {
                $anz ++;
            }
        }

        if ($anz < 6) {
            $dist = ' ';
            $fontsize = '10px';
        }
        if ($anz < 3) {
            $fontsize = '12px';
        }

        foreach ($_SESSION['pSudokuHelper']['sudoku'][$row][$col]['possible'] as $key => $data) {
            if ($data) {
                $text .= $key . $dist;
            }
        }

        $ret .= '<button class="openPopup" href="javascript:void(0);" style= "text-align: center' . $addColor . ';height: 60px;width:60px;font-size: ' . $fontsize . ' " data-href="' . SecurityUtils::encodeUrl(ADMIDIO_URL . FOLDER_PLUGINS . PLUGIN_FOLDER . '/system/assign.php', array(
            'row' => $row,
            'col' => $col
        )) . '">' . $text . '</button>';
    } else {

        $ret .= '<button class="openPopup" href="javascript:void(0);" style= "text-align: center' . $addColor . ';height: 60px;width:60px;font-size: 40px" data-href="' . SecurityUtils::encodeUrl(ADMIDIO_URL . FOLDER_PLUGINS . PLUGIN_FOLDER . '/system/assign.php', array(
            'row' => $row,
            'col' => $col
        )) . '">' . $_SESSION['pSudokuHelper']['sudoku'][$row][$col]['set'] . '</button>';
    }

    return $ret;
}

/**
 * Funktion gibt den Startindex eines Neunerblocks zurück,
 * z.B.
 * Zeile oder Spalte = 5, ergibt Startindex = 4,
 * z.B. Zeile oder Spalte = 9, ergibt Startindex = 7
 * (Info: Endindex ist Startindex + 2)
 *
 * @param int $field
 *            Feld (=Zeile oder Spalte)
 * @return int Startindex für diesen Neunerblock
 */
function novum($field)
{
    $ret = 0;

    if ($field < 4) {
        $ret = $field - ($field - 1);
    } elseif ($field > 6) {
        $ret = $field - ($field - 7);
    } else {
        $ret = $field - ($field - 4);
    }
    return $ret;
}

/**
 * Funktion sucht Zahlen
 * z.B.
 * wenn $anz=1, dann wird 2 gefunden in 12457 145 458 458
 * z.B. wenn $anz=2, dann wird 35 gefunden in 123458 2687 23458 478 4789
 *
 * @param int $anz
 *            Anzahl der zu suchenden Zahlen
 * @param array $arbArray
 *            das übergebene Array in dem die Zahlen gesucht werden
 * @return array Array mit den gefundenen Zahlen
 */
function search_numbers($anz, $arbArray)
{
    $ret = array();
    $foundArray = array();

    // im ersten Schritt die Zahlen löschen, deren (Gesamt)Anzahl nicht stimmt
    // wenn z.B. nach Pärchen gesucht wird ($anz=2), dann darf eine Zahl (1 bis 9) nur 2x vorkommen
    for ($possible = 1; $possible < 10; $possible ++) {
        $possible_count = 0;
        $foundArray[$possible] = '';

        for ($i = 1; $i < 10; $i ++) {
            if (! $arbArray[$i][$possible]) {
                continue;
            } else {
                $possible_count ++;
                $foundArray[$possible] .= $i;
            }
        }

        if ($possible_count != $anz) {
            for ($i = 1; $i < 10; $i ++) {
                $arbArray[$i][$possible] = false;
            }
            unset($foundArray[$possible]);
        }
    }

    $tempArray = array_count_values($foundArray);

    // jetzt die Zahlen löschen, die nicht im selben "Kästchen" sind
    // wenn z.B. nach Pärchen gesucht wird, müssen die Zahlen 35 im selben Kästchen sein 123547 3578 (=OK), 12378 1578 123578 (=NEIN)
    foreach ($tempArray as $colFound => $count) {
        if ($count != $anz) {
            foreach (array_keys($foundArray, $colFound) as $key) {
                unset($foundArray[$key]);
            }
        }
    }

    // jetzt das Rückgabearray zusammensetzen
    if (sizeof($foundArray) > 0) {
        foreach ($foundArray as $key => $data) {
            $ret[$data][] = $key;
        }
    }

    return $ret;
}

/**
 * Funktion initialisiert ein neues Spiel
 *
 * @param
 *            none
 */
function initSudoku()
{
    $_SESSION['pSudokuHelper'] = array();
    $_SESSION['pSudokuHelper']['backup'] = array();
    $_SESSION['pSudokuHelper']['stepback'] = array();
    $_SESSION['pSudokuHelper']['previous'] = array();

    for ($row = 1; $row < 10; $row ++) {
        for ($col = 1; $col < 10; $col ++) {
            $_SESSION['pSudokuHelper']['sudoku'][$row][$col] = array(
                'possible' => array_fill(1, 9, true),
                'set' => 0
            );
        }
    }
    updateStepback();
}

/**
 * Funktion fügt den aktuellen Sudoku-Stand an das StepBack-Array an
 *
 * @param
 *            none
 */
function updateStepback()
{
    $_SESSION['pSudokuHelper']['stepback'][] = $_SESSION['pSudokuHelper']['sudoku'];
}

/**
 * Funktion erzeugt aus dem übergebenen Wert die Startzeile oder die Startspalte
 * Bsp:
 * 1 ergibt 1
 * 3 ergibt 1
 * 4 ergibt 4
 * 6 ergibt 4
 * 8 ergibt 7
 *
 * @param
 *            $val
 *            
 */
function getStartRowOrCol($val)
{
    return (3 * ceil($val / 3)) - 2;
}

/**
 * Funktion setzt einen Wert im 'set'-Bereich des SESSION-Arrays 'pSudokuHelper'
 * Wird ein Wert ungeleich 0 gesetzt, so wird das gesamte, dazugehörige 'possible'-Array auf false gesetzt
 *
 * @param int $xPos
 *            die X-Position für den zu setzenden Wert
 * @param int $yPos
 *            die Y-Position für den zu setzenden Wert
 * @param int $number
 *            die Zahl, die an dieser Position gesetzt werden soll (default: 0)
 */
function setNumber(int $xPos, int $yPos, int $number = 0): void
{
    $_SESSION['pSudokuHelper']['sudoku'][$xPos][$yPos]['set'] = $number;
    if ($number != 0) {
        $_SESSION['pSudokuHelper']['sudoku'][$xPos][$yPos]['possible'] = array_fill(1, 9, false);
    }
}

/**
 * Funktion setzt einen Wert oder mehrere Wert im 'possible'-Bereich
 * des SESSION-Arrays 'pSudokuHelper'
 *
 * @param int $xPos
 *            die X-Position für den zu setzenden Wert
 * @param int $yPos
 *            die Y-Position für den zu setzenden Wert
 * @param int $number
 *            die Position, die auf true oder false gesetzt wird
 *            0: alle Positionen
 *            1-9: Position
 * @param bool $val
 *            true oder false (default: true)
 */
function setPossible(int $xPos, int $yPos, int $number, bool $val = true): void
{
    if ($number === 0) {
        $_SESSION['pSudokuHelper']['sudoku'][$xPos][$yPos]['possible'] = array_fill(1, 9, $val);
    } else {
        $_SESSION['pSudokuHelper']['sudoku'][$xPos][$yPos]['possible'][$number] = $val;
    }
}

/**
 * Funktion setzt den aktuellen Sudoku-Stand gleich mit dem Previous-Array
 *
 * @param
 *            none
 */
function updatePrevious()
{
    $_SESSION['pSudokuHelper']['previous'] = $_SESSION['pSudokuHelper']['sudoku'];
}
