<?php
/**
 ***********************************************************************************************
 * Verarbeiten der Einstellungen des Admidio-Plugins sudokuhelper
 *
 * @copyright 2004-2019 rmb
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2.0 only
 ***********************************************************************************************
 */

/******************************************************************************
 * Parameters:
 *
 * mode     : single  - find single numbers
 *            backup  - save the game
 *            restore - restore the game
 *            
 * id       : id of the game to restore
 *
 *****************************************************************************/

require_once(__DIR__ . '/../../adm_program/system/common.php');
require_once(__DIR__ . '/common_function.php');

// Initialize and check the parameters
$getMode = admFuncVariableIsValid($_GET, 'mode', 'string');
$getId   = admFuncVariableIsValid($_GET, 'id', 'string');

switch($getMode)
{            	
    case 'single':
	
        //9er Zeile prüfen
	   for ($row = 1; $row < 10; $row++)
	   {
	       for ($possible = 1; $possible < 10; $possible++)
	       {
	           $possible_count = 0;
	           $possible_found = 0;
	       
	           for ($col = 1; $col < 10; $col++)
	           {
	               if (!$_SESSION['pSudokuHelper']['sudoku'][$row][$col]['possible'][$possible])
	               {
	                   continue;
	               }
	               else
	               {
	                   $possible_count++;
	                   $possible_found = $col;
	               }
	           }
	       
	           if ($possible_count == 1)
	           {
	               $_SESSION['pSudokuHelper']['sudoku'][$row][$possible_found]['possible'] = array_fill(1,9,false);
	               $_SESSION['pSudokuHelper']['sudoku'][$row][$possible_found]['possible'][$possible] = true;
	           }
	        }
	    }
	
	    //9er Spalte prüfen
        for ($col = 1; $col < 10; $col++)
		{
		    for ($possible = 1; $possible < 10; $possible++)
		    {
		        $possible_count = 0;
		        $possible_found = 0;
		        
		        for ($row = 1; $row < 10; $row++)
		        {
		            if (!$_SESSION['pSudokuHelper']['sudoku'][$row][$col]['possible'][$possible])
		            {
		                continue;
		            }
		            else
		            {
		                $possible_count++;
		                $possible_found = $row;
		            }
		        }
		        
		        if ($possible_count == 1)
		        {
		            $_SESSION['pSudokuHelper']['sudoku'][$row][$possible_found]['possible'] = array_fill(1,9,false);
		            $_SESSION['pSudokuHelper']['sudoku'][$row][$possible_found]['possible'][$possible] = true;
		        }
		    }
		}
		
		//9er Block prüfen
		$neunerBlock = array();
		$neunerBlock['ol'] = array('row'=> 1 , 'col'=> 1);      // ol = oben links
		$neunerBlock['om'] = array('row'=> 1 , 'col'=> 4);      // om = oben mitte
		$neunerBlock['or'] = array('row'=> 1 , 'col'=> 7);      // or = oben rechts
		$neunerBlock['ml'] = array('row'=> 4 , 'col'=> 1);      // ml = mitte links
		$neunerBlock['mm'] = array('row'=> 4 , 'col'=> 4);      // mm = mitte mitte
		$neunerBlock['mr'] = array('row'=> 4 , 'col'=> 7);      // mr = mitte rechts
		$neunerBlock['ul'] = array('row'=> 7 , 'col'=> 1);      // ul = unten links
		$neunerBlock['um'] = array('row'=> 7 , 'col'=> 4);      // um = unten mitte
		$neunerBlock['ur'] = array('row'=> 7 , 'col'=> 7);      // ur = unten rechts
		
		foreach ($neunerBlock as $block => $blockData)
		{
            for ($possible = 1; $possible < 10; $possible++)
		    {
                $possible_count = 0;
		        $possible_found_row = 0;
		        $possible_found_col = 0;
		        
		        for ($row = $blockData['row'] = 1; $row < $blockData['row']+3; $row++)
		        {
		            for ($col = $blockData['col']; $col < $blockData['col']+3; $col++)
		            {
		                if ($_SESSION['pSudokuHelper']['sudoku'][$row][$col]['possible'][$possible])
		                {
		                    $possible_count++;
		                    $possible_found_row = $row;
		                    $possible_found_col = $col;
		                }
		            }
		        }
		        if ($possible_count == 1)
		        {
		            $_SESSION['pSudokuHelper']['sudoku'][$possible_found_row][$possible_found_col]['possible'] = array_fill(1,9,false);
		            $_SESSION['pSudokuHelper']['sudoku'][$possible_found_row][$possible_found_col]['possible'][$possible] = true;
		        } 
		    }
        }
        break;
    case 'backup':
        $_SESSION['pSudokuHelper']['backup'][DATETIME_NOW] = $_SESSION['pSudokuHelper']['sudoku'];
        break;
        
    case 'restore':
        $_SESSION['pSudokuHelper']['sudoku'] = $_SESSION['pSudokuHelper']['backup'][$getId];
        break;
        
}    	

admRedirect(ADMIDIO_URL . FOLDER_PLUGINS . PLUGIN_FOLDER. '/sudokuhelper.php');
