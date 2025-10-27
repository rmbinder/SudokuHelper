<?php
/**
 ***********************************************************************************************
 * Gemeinsame Funktionen fuer das Admidio-Plugin sudokuhelper
 *
 * @copyright rmb
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2.0 only
 ***********************************************************************************************
 */

use Admidio\Infrastructure\Utils\SecurityUtils;

if (basename($_SERVER['SCRIPT_FILENAME']) === 'common_function.php') {
    exit('This page may not be called directly!');
}

require_once(__DIR__ . '/../../../system/common.php');

$folders = explode('/', $_SERVER['SCRIPT_FILENAME']);
while (array_search(substr(FOLDER_PLUGINS, 1), $folders))
{
    array_shift($folders);
}
array_shift($folders);

if(!defined('PLUGIN_FOLDER'))
{
    define('PLUGIN_FOLDER', '/'.$folders[0]);
}
unset($folders);

/**
 * Funktion erzeugt einen Button mit Link
 * @param   string  $row  Zeile des Buttons
 * @param   string  $col  Spalte des Buttons
 * @return  string  html-Code mit Link für einen Button
 */
function generate_button($row, $col)
{
    $ret = '';
    $text = '';
   
    if ($_SESSION['pSudokuHelper']['sudoku'][$row][$col]['set'] == 0)
    {
        $dist = '';
        $anz = 0;
        $fontsize = '8px';
        
        foreach ($_SESSION['pSudokuHelper']['sudoku'][$row][$col]['possible'] as $key => $data)
        {
            if ($data)
            {
                $anz++;
            }
        }
        
        if ($anz < 6)
        {
            $dist = ' ';
        	$fontsize = '10px';
        }
        if ($anz < 3)
        {
        	$fontsize = '12px';
        }

        foreach ($_SESSION['pSudokuHelper']['sudoku'][$row][$col]['possible'] as $key => $data)
        {
            if ($data)
            {
                $text .= $key.$dist;
            }
        }

        $ret .= '<button class="openPopup" href="javascript:void(0);" style= "text-align: center;height: 60px;width:60px;font-size: '.$fontsize.' " data-href="'.SecurityUtils::encodeUrl(ADMIDIO_URL . FOLDER_PLUGINS . PLUGIN_FOLDER .'/system/assign.php', array('row' => $row, 'col' => $col)) . '">'. $text .'</button>';
    }
    else
    {
        $ret .= '<button class="openPopup" href="javascript:void(0);" style= "text-align: center;height: 60px;width:60px;font-size: 40px" data-href="'.SecurityUtils::encodeUrl(ADMIDIO_URL . FOLDER_PLUGINS . PLUGIN_FOLDER .'/system/assign.php', array('row' => $row, 'col' => $col)) . '">'. $_SESSION['pSudokuHelper']['sudoku'][$row][$col]['set'] .'</button>';
    }
    
    return $ret;
}

/**
 * Funktion gibt den Startindex eines Neunerblocks zurück,
 * z.B. Zeile oder Spalte = 5, ergibt Startindex = 4,
 * z.B. Zeile oder Spalte = 9, ergibt Startindex = 7
 * (Info: Endindex ist Startindex + 2)
 * @param   int  $field   Feld (=Zeile oder Spalte)
 * @return  int  Startindex für diesen Neunerblock
 */
function novum($field)
{
    $ret = 0;
    
    if ($field < 4)
    {
        $ret = $field -($field-1);
    }
    elseif ($field > 6)
    {
        $ret = $field -($field-7);
    }
    else
    {
        $ret = $field -($field-4);
    }
    return $ret;
}

/**
 * Funktion sucht Zahlen
 * z.B. wenn $anz=1, dann wird 2 gefunden in 12457 145 458 458
 * z.B. wenn $anz=2, dann wird 35 gefunden in 123458 2687 23458 478 4789
 * @param   int  $anz  Anzahl der zu suchenden Zahlen
 * @param   array  $arbArray  das übergebene Array in dem die Zahlen gesucht werden
 * @return  array  Array mit den gefundenen Zahlen
 */
function search_numbers($anz, $arbArray)
{
    $ret = array();
    $foundArray = array();
    
    // im ersten Schritt die Zahlen löschen, deren (Gesamt)Anzahl nicht stimmt
    // wenn z.B. nach Pärchen gesucht wird ($anz=2), dann darf eine Zahl (1 bis 9) nur 2x vorkommen
    for ($possible = 1; $possible < 10; $possible++)
    {
        $possible_count = 0;
        $foundArray[$possible] = '';
        
        for ($i = 1; $i < 10; $i++)
        {
            if (!$arbArray[$i][$possible])
            {
                continue;
            }
            else
            {
                $possible_count++;
                $foundArray[$possible] .= $i;
            }
        }
        
        if ($possible_count <> $anz)
        {
            for ($i = 1; $i < 10; $i++)
            {
                $arbArray[$i][$possible] = false;
            }
            unset($foundArray[$possible]);
        }
    }
    
    $tempArray = array_count_values($foundArray);
    
    // jetzt die Zahlen löschen, die nicht im selben "Kästchen" sind
    // wenn z.B. nach Pärchen gesucht wird, müssen die Zahlen 35 im selben Kästchen sein 123547 3578 (=OK), 12378 1578 123578 (=NEIN)
    foreach ($tempArray as $colFound => $count)
    {
        if ($count <> $anz)
        {
            foreach (array_keys($foundArray, $colFound) as $key)
            {
                unset($foundArray[$key]);
            }
        }
    }
    
    // jetzt das Rückgabearray zusammensetzen
    if (sizeof($foundArray) > 0)
    {
        foreach ($foundArray as $key => $data)
        {
            $ret[$data][] = $key;
        }
    }
    
    return $ret;
}

/**
 * Funktion initialisiert ein neues Spiel
 * @param   none
 */
function initSudoku()
{
    $_SESSION['pSudokuHelper'] = array();
    $_SESSION['pSudokuHelper']['backup'] = array();
    $_SESSION['pSudokuHelper']['stepback'] = array();
    
    for ($row = 1; $row < 10; $row++)
    {
        for ($col = 1; $col < 10; $col++)
        {
            $_SESSION['pSudokuHelper']['sudoku'][$row][$col] = array('possible' => array_fill(1,9,true), 'set' => 0);
        }
    }
    updateStepback();
}

/**
 * Funktion fügt den aktuellen Sudoku-Stand an das StepBack-Array an
 * @param   none
 */
function updateStepback()
{
    $_SESSION['pSudokuHelper']['stepback'][] = $_SESSION['pSudokuHelper']['sudoku'];
}
