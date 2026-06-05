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
