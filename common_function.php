<?php
/**
 ***********************************************************************************************
 * Gemeinsame Funktionen fuer das Admidio-Plugin sudokuhelper
 *
 * @copyright 2004-2019 rmb
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2.0 only
 ***********************************************************************************************
 */

require_once(__DIR__ . '/../../adm_program/system/common.php');

if (!defined('PLUGIN_FOLDER'))
{
	define('PLUGIN_FOLDER', '/'.substr(__DIR__,strrpos(__DIR__,DIRECTORY_SEPARATOR)+1));
}

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
   
    if ($_SESSION['pSudokuHelper'][$row][$col]['set'] == 0)
    {
        $dist = '';
        $anz = 0;
        $fontsize = '8px';
        
        foreach ($_SESSION['pSudokuHelper'][$row][$col]['possible'] as $key => $data)
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

        foreach ($_SESSION['pSudokuHelper'][$row][$col]['possible'] as $key => $data)
        {
            if ($data)
            {
                $text .= $key.$dist;
            }
        }

        $ret .= '<button class="button" data-toggle="modal" data-target="#admidio_modal"  style= "height: 60px;width:60px;font-size: '.$fontsize.' " href="'.safeUrl(ADMIDIO_URL . FOLDER_PLUGINS . PLUGIN_FOLDER .'/assign.php', array('row' => $row, 'col' => $col)) . '">'. $text .'</button>';
    }
    else
    {
        $ret .= '<button class="button" data-toggle="modal" data-target="#admidio_modal"  style= "height: 60px;width:60px;font-size: 40px" href="'.safeUrl(ADMIDIO_URL . FOLDER_PLUGINS . PLUGIN_FOLDER .'/assign.php', array('row' => $row, 'col' => $col)) . '">'. $_SESSION['pSudokuHelper'][$row][$col]['set'] .'</button>';
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
 * Funktion erzeugt einen Button mit Link eingebettet in <tr><td> einer Tabelle
 * @param   string  $url  URL des Links
 * @param   string  $text  Anzeigetext des Buttons
 * @return  string  html-Code mit Link für einen Button 
 */
function function_button($url, $text)
{
    $ret = '<tr>';
    $ret .= '<td>';
    $ret .= '<button type="button" class="btn btn-default" onclick="window.location.href=\''.$url.'\'">'.$text.'</button>';
    $ret .= '</td>';
    $ret .= '</tr>';
    
    return $ret;
}

/**
 * Funktion erzeugt eine Leerzeile in einer Tabelle eingebettet in <tr><td> einer Tabelle
 * @param   none
 * @return  string  html-Code mit Link für einen Button
 */
function emptyLine()
{
    $ret = '<tr>';
    $ret .= '<td>';
    $ret .= '&nbsp';
    $ret .= '</td>';
    $ret .= '</tr>';
    
    return $ret;
}