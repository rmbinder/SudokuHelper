<?php
/**
 ***********************************************************************************************
 * SudokuHelper
 *
 * Version 3.1
 * 
 * Stand 17.02.2026
 *
 * Dieses Admidio-Plugin hilft beim Lösen eines Sudoku-Rätsels.
 * 
 * This admidio plugin helps solve a Sudoku puzzle.
 * 
 * Author: rmb
 *
 * Compatible with Admidio version 5
 *
 * @copyright rmb
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2.0 only
 *   
 ***********************************************************************************************
 */

use Admidio\Infrastructure\Utils\SecurityUtils;
use Admidio\Infrastructure\Exception;
use Plugins\SudokuHelper\classes\Config\ConfigTable;
 
try {
    require_once (__DIR__ . '/../../system/common.php');
    require_once (__DIR__ . '/system/common_function.php');

    $gNavigation->addStartUrl(CURRENT_URL);

    $pPreferences = new ConfigTable();
    if ($pPreferences->checkforupdate()) {
        $pPreferences->init();
    }

    $pPreferences->read();
    if ($pPreferences->config['install']['access_role_id'] == 0 || $pPreferences->config['install']['menu_item_id'] == 0) {

        $urlInst = ADMIDIO_URL . FOLDER_PLUGINS . PLUGIN_FOLDER . '/system/install.php';
        $gMessage->show($gL10n->get('PLG_SUDOKU_HELPER_INSTALL_UPDATE_REQUIRED', array(
            '<a href="' . $urlInst . '">' . $urlInst . '</a>'
        )));
    }

    admRedirect(ADMIDIO_URL . FOLDER_PLUGINS . PLUGIN_FOLDER . '/system/sudokuhelper.php');
} catch (Exception $e) {
    $gMessage->show($e->getMessage());
}
