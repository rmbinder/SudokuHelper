<?php
/**
 ***********************************************************************************************
 * Assign the new values
 *
 * @copyright 2004-2019 rmb
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2.0 only
 ***********************************************************************************************
 */

/******************************************************************************
 * Parameters:
 *
 * row : row of the pressed button
 * col : column of the pressed button
 * set : the new number for the pressed button
 *
 *****************************************************************************/

require_once(__DIR__ . '/../../adm_program/system/common.php');
require_once(__DIR__ . '/common_function.php');

// Initialize and check the parameters
$getRow  = admFuncVariableIsValid($_GET, 'row', 'int');
$getCol  = admFuncVariableIsValid($_GET, 'col', 'int');
$postSet = admFuncVariableIsValid($_POST, 'set', 'string', array('defaultValue' => '0'));

$gMessage->showHtmlTextOnly(true);

if ($postSet != '0')
{
    $_SESSION['pSudokuHelper']['sudoku'][$getRow][$getCol]['set'] = $postSet;
    $_SESSION['pSudokuHelper']['sudoku'][$getRow][$getCol]['possible'] = array_fill(1,9,false);
    
    for ($row = 1; $row < 10; $row++)
    {
        $_SESSION['pSudokuHelper']['sudoku'][$row][$getCol]['possible'][$postSet] = false;
    }
    
    for ($col = 1; $col < 10; $col++)
    {
        $_SESSION['pSudokuHelper']['sudoku'][$getRow][$col]['possible'][$postSet] = false;
    }
    
    for ($row = novum($getRow); $row < novum($getRow)+3; $row++)
    {
        for ($col = novum($getCol); $col < novum($getCol)+3; $col++)
        {
            $_SESSION['pSudokuHelper']['sudoku'][$row][$col]['possible'][$postSet] = false;
        }
    }
}
else
{
    for ($conf = 1; $conf < 10; $conf++)
    {
        if (isset($_POST['possible-'. $conf]))
        {
            $_SESSION['pSudokuHelper']['sudoku'][$getRow][$getCol]['possible'][$conf] = true;
        }
        else
        {
            $_SESSION['pSudokuHelper']['sudoku'][$getRow][$getCol]['possible'][$conf] = false;
        }
    }
}

echo 'success';
