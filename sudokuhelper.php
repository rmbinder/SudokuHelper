<?php
/**
 ***********************************************************************************************
 * sudokuhelper
 *
 * Version 1.0
 * 
 * Stand 04.05.2019
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
    for ($row = 1; $row < 10; $row++)
    {
        for ($col = 1; $col < 10; $col++)
        {
            $_SESSION['pSudokuHelper'][$row][$col] = array('possible' => array_fill(1,9,true), 'set' => 0);
        }
    }
}

$headline = $gL10n->get('PLG_SUDOKU_HELPER_NAME');

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

$html = '<table>';

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

$page->addHtml($html);
$page->addHtml($form->show(false));
$page->show();
