<?php
/**
 ***********************************************************************************************
 * SudokuHelper
 *
 * Version 2.2
 * 
 * Stand 18.01.2024
 *
 * Dieses Admidio-Plugin hilft beim Lösen eines Sudoku-Rätsels.
 * This admidio plugin helps solve a Sudoku puzzle.
 * 
 * Author: rmb
 *
 * Compatible with Admidio from version 4.0
 *
 * @copyright rmb
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2.0 only
 *   
 ***********************************************************************************************
 */

use Admidio\Infrastructure\Utils\SecurityUtils;

require_once(__DIR__ . '/../../system/common.php');
require_once(__DIR__ . '/common_function.php');
include(__DIR__ . '/version.php');

if (!isset($_SESSION['pSudokuHelper']))
{
    initSudoku();
}

$successCounter = 0;

$headline = $gL10n->get('PLG_SUDOKU_HELPER_NAME');

$gNavigation->addStartUrl(CURRENT_URL);

// create html page object
$page = new HtmlPage('plg-sudokuhelper', $headline.' <small>v'.$plugin_version.'</small>');

$page->addPageFunctionsMenuItem('admSudokuHelperMenuItemSingle', $gL10n->get('PLG_SUDOKU_HELPER_FIND_SINGLE'), SecurityUtils::encodeUrl(ADMIDIO_URL . FOLDER_PLUGINS . PLUGIN_FOLDER .'/sudokuhelper_function.php', array('mode' => 'find_equals', 'anz' => 1)), 'fa-dice-one');
$page->addPageFunctionsMenuItem('admSudokuHelperMenuItemCouple', $gL10n->get('PLG_SUDOKU_HELPER_FIND_COUPLES'), SecurityUtils::encodeUrl(ADMIDIO_URL . FOLDER_PLUGINS . PLUGIN_FOLDER .'/sudokuhelper_function.php', array('mode' => 'find_equals', 'anz' => 2)), 'fa-dice-two');
$page->addPageFunctionsMenuItem('admSudokuHelperMenuItemTrible', $gL10n->get('PLG_SUDOKU_HELPER_FIND_TRIBLE'), SecurityUtils::encodeUrl(ADMIDIO_URL . FOLDER_PLUGINS . PLUGIN_FOLDER .'/sudokuhelper_function.php', array('mode' => 'find_equals', 'anz' => 3)), 'fa-dice-three');
$page->addPageFunctionsMenuItem('admSudokuHelperMenuItemPattern', $gL10n->get('PLG_SUDOKU_HELPER_PATTERN'), SecurityUtils::encodeUrl(ADMIDIO_URL . FOLDER_PLUGINS . PLUGIN_FOLDER .'/sudokuhelper_function.php', array('mode' => 'set')), 'fa-cube');
$page->addPageFunctionsMenuItem('admSudokuHelperMenuItemBackup', $gL10n->get('PLG_SUDOKU_HELPER_CREATE_BACKUP'), SecurityUtils::encodeUrl(ADMIDIO_URL . FOLDER_PLUGINS . PLUGIN_FOLDER .'/sudokuhelper_function.php', array('mode' => 'backup')), 'fa-save');

if (sizeof($_SESSION['pSudokuHelper']['backup']) > 0)
{
    $page->addPageFunctionsMenuItem('menu_item_restore', $gL10n->get('PLG_SUDOKU_HELPER_RESTORE_BACKUP'), '#', 'fa-reply');
    foreach ($_SESSION['pSudokuHelper']['backup'] as $backup => $dummy)
    {
        $page->addPageFunctionsMenuItem('menu_item_restore'.$backup, $backup, SecurityUtils::encodeUrl(ADMIDIO_URL . FOLDER_PLUGINS . PLUGIN_FOLDER .'/sudokuhelper_function.php', array('mode' => 'restore', 'id' => $backup)), 'fa-reply', 'menu_item_restore');
    }
}
if (sizeof($_SESSION['pSudokuHelper']['stepback']) > 1)
{
    $page->addPageFunctionsMenuItem('admSudokuHelperMenuItemStepback', $gL10n->get('PLG_SUDOKU_HELPER_STEP_BACK').' ('.(sizeof($_SESSION['pSudokuHelper']['stepback'])-1).')', SecurityUtils::encodeUrl(ADMIDIO_URL . FOLDER_PLUGINS . PLUGIN_FOLDER .'/sudokuhelper_function.php', array('mode' => 'stepback')), 'fa-undo-alt');
}

$html = '<div style="margin: 0 auto;">';

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
        
        $successCounter += $_SESSION['pSudokuHelper']['sudoku'][$row][$col]['set'];
    }
    $html .= '</tr>';
}

$html .= '</table>';
$html .= '<br style="clear:both;"><br />';
$html .= '</div>';

if ($successCounter == 405)
{
    sleep(1);
    echo '
        <audio autoplay>
            <source src="'.ADMIDIO_URL . FOLDER_PLUGINS . PLUGIN_FOLDER .'/sounds/tusch'.rand(1,5).'.mp3" type="audio/mp3" />
        </audio>
    ';
    
    initSudoku();
    
    $gMessage->setForwardYesNo(SecurityUtils::encodeUrl(ADMIDIO_URL . FOLDER_PLUGINS . PLUGIN_FOLDER .'/sudokuhelper.php'));
    $gMessage->show($gL10n->get('PLG_SUDOKU_HELPER_SUCCESS_MESSAGE'),$gL10n->get('PLG_SUDOKU_HELPER_CONGRATULATIONS'));
}

$page->addHtml($html);
$page->show();
