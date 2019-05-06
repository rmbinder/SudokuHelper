<?php
/**
 ***********************************************************************************************
 * sudokuhelper
 *
 * Version 1.1-Beta1
 * 
 * Stand 05.05.2019
 *
 * Dieses Admidio-Plugin hilft beim Lösen eines Sudoku-Rätsels.
 * 
 * Author: rmb
 *
 * Compatible with Admidio version 3.3
 *
 * @copyright 2004-2019 rmb
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2.0 only
 *   
 ***********************************************************************************************
 */

require_once(__DIR__ . '/../../adm_program/system/common.php');
require_once(__DIR__ . '/common_function.php');

if (!isset($_SESSION['pSudokuHelper']))
{
    $_SESSION['pSudokuHelper'] = array();
    $_SESSION['pSudokuHelper']['backup'] = array();
    
    for ($row = 1; $row < 10; $row++)
    {
        for ($col = 1; $col < 10; $col++)
        {
            $_SESSION['pSudokuHelper']['sudoku'][$row][$col] = array('possible' => array_fill(1,9,true), 'set' => 0);
        }
    }
}

$headline = $gL10n->get('PLG_SUDOKU_HELPER_NAME');

$gNavigation->addUrl(CURRENT_URL);

// create html page object
$page = new HtmlPage($headline);
$page->enableModal();

$page->addJavascript('
    $("body").on("hidden.bs.modal", ".modal", function() {
        $(this).removeData("bs.modal");
        location.reload();
    });
    ',
    true
    );

// create the form
$form = new HtmlForm('sudoku_form', null, $page, array('class' => 'form-preferences'));

$html .= '<div style="margin: 0 auto;">';

//Tabelle für Sudoku
$html .= '<table style="float:left;">';

for ($row = 1; $row < 10; $row++)
{
    if ($row == 4 || $row == 7)
    {
        $html .= '<tr>';
        $html .= '<td>';
        $html .= '&nbsp';
        $html .= '</td>';
        $html .= '</tr>';
    }
    
    $html .= '<tr>';
    for ($col = 1; $col < 10; $col++)
    {
        if ($col == 4 || $col == 7)
        {
            $html .= '<td>';
            $html .= '&nbsp&nbsp&nbsp';
            $html .= '</td>';
        }
        $html .= '<td>';
        $html .= generate_button($row, $col);
        $html .= '</td>';
    }
    $html .= '</tr>';
}

$html .= '</table>';

//Tabelle für Zusatzdaten
$html .= '<table style="float:right; " border="0">';

$html .= function_button(safeUrl(ADMIDIO_URL . FOLDER_PLUGINS . PLUGIN_FOLDER .'/sudokuhelper_function.php', array('mode' => 'single')), $gL10n->get('PLG_SUDOKU_HELPER_FIND_SINGLE'));
$html .= emptyLine();
$html .= function_button(safeUrl(ADMIDIO_URL . FOLDER_PLUGINS . PLUGIN_FOLDER .'/sudokuhelper_function.php', array('mode' => 'backup')), $gL10n->get('PLG_SUDOKU_HELPER_CREATE_BACKUP'));

if (sizeof($_SESSION['pSudokuHelper']['backup']) > 0)
{
    foreach ($_SESSION['pSudokuHelper']['backup'] as $backup => $dummy)
    {
        $html .= function_button(safeUrl(ADMIDIO_URL . FOLDER_PLUGINS . PLUGIN_FOLDER .'/sudokuhelper_function.php', array('mode' => 'restore', 'id' => $backup)), $backup);
    }
}

$html .= '</table>';
$html .= '<br style="clear:both;">';
$html .= '</div>';

$page->addHtml($html);
$page->addHtml($form->show(false));
$page->show();
