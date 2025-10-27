<?php
/**
 ***********************************************************************************************
 * SudokuHelper
 *
 * Version 3.0
 * 
 * Stand 27.10.2025
 *
 * Dieses Admidio-Plugin hilft beim Lösen eines Sudoku-Rätsels.
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

try {
    require_once (__DIR__ . '/../../system/common.php');
    require_once (__DIR__ . '/system/common_function.php');
    include (__DIR__ . '/system/version.php');

    if (! isset($_SESSION['pSudokuHelper'])) {
        initSudoku();
    }

    admRedirect(ADMIDIO_URL . FOLDER_PLUGINS . PLUGIN_FOLDER . '/system/sudokuhelper.php');
} catch (Exception $e) {
    $gMessage->show($e->getMessage());
}
