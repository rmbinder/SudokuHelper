<?php
/**
 ***********************************************************************************************
 * Assign the new values
 *
 * @copyright rmb
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2.0 only
 ***********************************************************************************************
 */

/**
 * ****************************************************************************
 * Parameters:
 *
 * row : row of the pressed button
 * col : column of the pressed button
 * set : the new number for the pressed button
 *
 * ***************************************************************************
 */
require_once (__DIR__ . '/../../../system/common.php');
require_once (__DIR__ . '/common_function.php');

// Initialize and check the parameters
$getRow = admFuncVariableIsValid($_GET, 'row', 'int');
$getCol = admFuncVariableIsValid($_GET, 'col', 'int');
$postSet = admFuncVariableIsValid($_POST, 'set', 'string', array(
    'defaultValue' => '0'
));

$gMessage->showHtmlTextOnly(true);
updatePrevious();

if ($postSet != '0') {
    setNumber($getRow, $getCol, $postSet);

    for ($row = 1; $row < 10; $row ++) {
        setPossible($row, $getCol, $postSet, false);
    }

    for ($col = 1; $col < 10; $col ++) {
        setPossible($getRow, $col, $postSet, false);
    }

    for ($row = novum($getRow); $row < novum($getRow) + 3; $row ++) {
        for ($col = novum($getCol); $col < novum($getCol) + 3; $col ++) {
            setPossible($row, $col, $postSet, false);
        }
    }
} else {
    for ($conf = 1; $conf < 10; $conf ++) {
        if (isset($_POST['possible-' . $conf])) {
            setPossible($getRow, $getCol, $conf, true);
        } else {
            setPossible($getRow, $getCol, $conf, false);
        }
    }
}

updateStepback();

echo 'success';
