<?php
/**
 ***********************************************************************************************
 * Verarbeiten der Einstellungen des Admidio-Plugins sudokuhelper
 *
 * @copyright 2004-2020 rmb
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2.0 only
 ***********************************************************************************************
 */

/******************************************************************************
 * Parameters:
 *
 * mode     : find_equals   - find numbers  
 *            set           - set an pattern sudoku (only for testing)
 *            backup        - save the game
 *            restore       - restore the game
 *
 * anz      : number for find_equals
 * 
 * id       : id of the game to restore
 *
 *****************************************************************************/

require_once(__DIR__ . '/../../adm_program/system/common.php');
require_once(__DIR__ . '/common_function.php');

// Initialize and check the parameters
$getMode = admFuncVariableIsValid($_GET, 'mode', 'string');
$getAnz  = admFuncVariableIsValid($_GET, 'anz', 'numeric');
$getId   = admFuncVariableIsValid($_GET, 'id', 'string');

switch($getMode)
{            	       
    case 'find_equals':
        
        // Zeilen und Spalten prüfen
        // wird horizontal geprüft, dann wird $koord1 durch $row und $koord2 durch $col ersetzt 
        // wird vertikal geprüft, dann wird $koord1 durch $col und $koord2 durch $row ersetzt 
        $rowToCol = true;  
        for ($i = 1; $i < 3; $i++)                  // for-Schleife wird 2x durchlaufen, einmal rowToCol, einmal colToRow
        {
            for ($koord1 = 1; $koord1 < 10; $koord1++)
            {
                $arbeitsarray = array();
                $found = array();
                for ($koord2 = 1; $koord2 < 10; $koord2++)
                {
                    $arbeitsarray[$koord2] = $_SESSION['pSudokuHelper']['sudoku'][($rowToCol ? $koord1 : $koord2)][($rowToCol ? $koord2 : $koord1)]['possible'];
                }
            
                $found = search_numbers($getAnz,$arbeitsarray);
               
                if (sizeof($found) > 0)
                {
                    foreach ($found as $key => $data)
                    {
                        for ($koord2 = 1; $koord2 < 10; $koord2++)
                        {
                            $result = array_intersect ( $data, array_keys($_SESSION['pSudokuHelper']['sudoku'][($rowToCol ? $koord1 : $koord2)][($rowToCol ? $koord2 : $koord1)]['possible'],true) );
                        
                            if (sizeof($result) > 0)                    // ja, in dieser Spalte gibt es eine Übereinstimmung
                            {
                                $_SESSION['pSudokuHelper']['sudoku'][($rowToCol ? $koord1 : $koord2)][($rowToCol ? $koord2 : $koord1)]['possible'] = array_fill(1,9,false);
                                foreach ($data as $datakey)
                                {
                                    $_SESSION['pSudokuHelper']['sudoku'][($rowToCol ? $koord1 : $koord2)][($rowToCol ? $koord2 : $koord1)]['possible'][$datakey] = true;
                                }    
                            }
                        }
                    }
                }
            }
            $rowToCol = false;                      // jetzt Durchlauf colToRow
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
            $arbeitsarray = array();
            $found = array();
            $i = 1;
                
            for ($row = $blockData['row']; $row < $blockData['row']+3; $row++)
            {
                for ($col = $blockData['col']; $col < $blockData['col']+3; $col++)
                {
                    $arbeitsarray[$i] = $_SESSION['pSudokuHelper']['sudoku'][$row][ $col]['possible'];
                    $i++;
                }
            }
            $found = search_numbers($getAnz,$arbeitsarray);
                
            if (sizeof($found) > 0)
            {
                foreach ($found as $key => $data)
                {
                    for ($row = $blockData['row']; $row < $blockData['row']+3; $row++)
                    {
                        for ($col = $blockData['col']; $col < $blockData['col']+3; $col++)
                        {
                            $result = array_intersect ($data, array_keys($_SESSION['pSudokuHelper']['sudoku'][$row ][$col]['possible'],true) );
                            
                            if (sizeof($result) > 0)                    // ja, in diesem Block gibt es eine Übereinstimmung
                            {
                                $_SESSION['pSudokuHelper']['sudoku'][$row ][$col]['possible'] = array_fill(1,9,false);
                                foreach ($data as $datakey)
                                {
                                    $_SESSION['pSudokuHelper']['sudoku'][$row ][$col]['possible'][$datakey] = true;
                                }
                            }
                        }
                    }
                }
            }
        }
        
        break;
        
    case 'set':
        
        initSudoku();
        $_SESSION['pSudokuHelper']['sudoku'][1][2]['set'] = 1;
        $_SESSION['pSudokuHelper']['sudoku'][1][3]['set'] = 5;
        $_SESSION['pSudokuHelper']['sudoku'][1][4]['set'] = 8;
        $_SESSION['pSudokuHelper']['sudoku'][1][6]['set'] = 7;
        $_SESSION['pSudokuHelper']['sudoku'][1][9]['set'] = 2;
        $_SESSION['pSudokuHelper']['sudoku'][2][4]['set'] = 4;
        $_SESSION['pSudokuHelper']['sudoku'][3][1]['set'] = 8;
        $_SESSION['pSudokuHelper']['sudoku'][3][2]['set'] = 7;
        $_SESSION['pSudokuHelper']['sudoku'][3][4]['set'] = 5;
        $_SESSION['pSudokuHelper']['sudoku'][3][6]['set'] = 2;
        $_SESSION['pSudokuHelper']['sudoku'][4][4]['set'] = 9;
        $_SESSION['pSudokuHelper']['sudoku'][4][8]['set'] = 8;
        $_SESSION['pSudokuHelper']['sudoku'][4][9]['set'] = 1;
        $_SESSION['pSudokuHelper']['sudoku'][5][2]['set'] = 3;
        $_SESSION['pSudokuHelper']['sudoku'][5][8]['set'] = 4;
        $_SESSION['pSudokuHelper']['sudoku'][5][9]['set'] = 9;
        $_SESSION['pSudokuHelper']['sudoku'][6][1]['set'] = 7;
        $_SESSION['pSudokuHelper']['sudoku'][6][2]['set'] = 9;
        $_SESSION['pSudokuHelper']['sudoku'][6][6]['set'] = 1;
        $_SESSION['pSudokuHelper']['sudoku'][7][2]['set'] = 8;
        $_SESSION['pSudokuHelper']['sudoku'][7][4]['set'] = 2;
        $_SESSION['pSudokuHelper']['sudoku'][7][6]['set'] = 4;
        $_SESSION['pSudokuHelper']['sudoku'][7][7]['set'] = 9;
        $_SESSION['pSudokuHelper']['sudoku'][7][8]['set'] = 1;
        $_SESSION['pSudokuHelper']['sudoku'][7][9]['set'] = 6;
        $_SESSION['pSudokuHelper']['sudoku'][8][6]['set'] = 9;
        $_SESSION['pSudokuHelper']['sudoku'][9][1]['set'] = 9;
        $_SESSION['pSudokuHelper']['sudoku'][9][4]['set'] = 1;
        $_SESSION['pSudokuHelper']['sudoku'][9][6]['set'] = 8;
        $_SESSION['pSudokuHelper']['sudoku'][9][7]['set'] = 5;
        $_SESSION['pSudokuHelper']['sudoku'][9][8]['set'] = 3;
        
        $_SESSION['pSudokuHelper']['sudoku'][1][1]['possible'] = array(1 => false ,2 => false ,3 => true ,4 => true  ,5 => false ,6 => true ,7 => false ,8 => false ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][1][2]['possible'] = array_fill(1,9,false);
        $_SESSION['pSudokuHelper']['sudoku'][1][3]['possible'] = array_fill(1,9,false);
        $_SESSION['pSudokuHelper']['sudoku'][1][4]['possible'] = array_fill(1,9,false);
        $_SESSION['pSudokuHelper']['sudoku'][1][5]['possible'] = array(1 => false ,2 => false ,3 => true ,4 => false ,5 => false ,6 => true ,7 => false ,8 => false ,9 => true  ) ;
        $_SESSION['pSudokuHelper']['sudoku'][1][6]['possible'] = array_fill(1,9,false);
        $_SESSION['pSudokuHelper']['sudoku'][1][7]['possible'] = array(1 => false ,2 => false ,3 => true ,4 => true  ,5 => false ,6 => true ,7 => false ,8 => false ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][1][8]['possible'] = array(1 => false ,2 => false ,3 => false,4 => false ,5 => false ,6 => true ,7 => false ,8 => false ,9 => true  ) ;
        $_SESSION['pSudokuHelper']['sudoku'][1][9]['possible'] = array_fill(1,9,false);
        
        $_SESSION['pSudokuHelper']['sudoku'][2][1]['possible'] = array(1 => false ,2 => true  ,3 => true ,4 => false ,5 => false ,6 => true ,7 => false ,8 => false ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][2][2]['possible'] = array(1 => false ,2 => true  ,3 => false,4 => false ,5 => false ,6 => true ,7 => false ,8 => false ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][2][3]['possible'] = array(1 => false ,2 => true  ,3 => true ,4 => false ,5 => false ,6 => true ,7 => false ,8 => false ,9 => true  ) ;
        $_SESSION['pSudokuHelper']['sudoku'][2][4]['possible'] = array_fill(1,9,false);
        $_SESSION['pSudokuHelper']['sudoku'][2][5]['possible'] = array(1 => true  ,2 => false ,3 => true ,4 => false ,5 => false ,6 => true ,7 => false ,8 => false ,9 => true  ) ;
        $_SESSION['pSudokuHelper']['sudoku'][2][6]['possible'] = array(1 => false ,2 => false ,3 => false ,4 => false ,5 => false ,6 => true ,7 => false ,8 => false ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][2][7]['possible'] = array(1 => true  ,2 => false ,3 => true ,4 => false ,5 => false ,6 => true ,7 => true  ,8 => true  ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][2][8]['possible'] = array(1 => false ,2 => false ,3 => false,4 => false ,5 => true  ,6 => true ,7 => true  ,8 => false ,9 => true  ) ;
        $_SESSION['pSudokuHelper']['sudoku'][2][9]['possible'] = array(1 => false ,2 => false ,3 => true ,4 => false ,5 => true  ,6 => false,7 => true  ,8 => true  ,9 => false ) ;
        
        $_SESSION['pSudokuHelper']['sudoku'][3][1]['possible'] = array_fill(1,9,false);
        $_SESSION['pSudokuHelper']['sudoku'][3][2]['possible'] = array_fill(1,9,false);
        $_SESSION['pSudokuHelper']['sudoku'][3][3]['possible'] = array(1 => false ,2 => false ,3 => true ,4 => true  ,5 => false ,6 => true ,7 => false ,8 => false ,9 => true  ) ;
        $_SESSION['pSudokuHelper']['sudoku'][3][4]['possible'] = array_fill(1,9,false);
        $_SESSION['pSudokuHelper']['sudoku'][3][5]['possible'] = array(1 => true  ,2 => false ,3 => true ,4 => false ,5 => false ,6 => true ,7 => false ,8 => false ,9 => true  ) ;
        $_SESSION['pSudokuHelper']['sudoku'][3][6]['possible'] = array_fill(1,9,false);
        $_SESSION['pSudokuHelper']['sudoku'][3][7]['possible'] = array(1 => true  ,2 => false ,3 => true ,4 => true  ,5 => false ,6 => true ,7 => false ,8 => false ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][3][8]['possible'] = array(1 => false ,2 => false ,3 => false,4 => false ,5 => false ,6 => true ,7 => false ,8 => false ,9 => true  ) ;
        $_SESSION['pSudokuHelper']['sudoku'][3][9]['possible'] = array(1 => false ,2 => false ,3 => true ,4 => true  ,5 => false ,6 => false,7 => false ,8 => false ,9 => false ) ;
        
        $_SESSION['pSudokuHelper']['sudoku'][4][1]['possible'] = array(1 => false ,2 => true  ,3 => false,4 => true  ,5 => true  ,6 => true ,7 => false ,8 => false ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][4][2]['possible'] = array(1 => false ,2 => true  ,3 => false,4 => true  ,5 => true  ,6 => true ,7 => false ,8 => false ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][4][3]['possible'] = array(1 => false ,2 => true  ,3 => false,4 => true  ,5 => false ,6 => true ,7 => false ,8 => false ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][4][4]['possible'] = array_fill(1,9,false);
        $_SESSION['pSudokuHelper']['sudoku'][4][5]['possible'] = array(1 => false ,2 => true  ,3 => false ,4 => true  ,5 => false  ,6 => false ,7 => false  ,8 => false ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][4][6]['possible'] = array(1 => false ,2 => false ,3 => true ,4 => false ,5 => false  ,6 => false ,7 => false ,8 => false ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][4][7]['possible'] = array(1 => false ,2 => false  ,3 => false ,4 => false ,5 => false ,6 => false ,7 => true  ,8 => false ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][4][8]['possible'] = array_fill(1,9,false);
        $_SESSION['pSudokuHelper']['sudoku'][4][9]['possible'] = array_fill(1,9,false);
        
        $_SESSION['pSudokuHelper']['sudoku'][5][1]['possible'] = array(1 => true  ,2 => true  ,3 => false,4 => false ,5 => true  ,6 => true ,7 => false ,8 => false ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][5][2]['possible'] = array_fill(1,9,false);
        $_SESSION['pSudokuHelper']['sudoku'][5][3]['possible'] = array(1 => true  ,2 => true  ,3 => false,4 => false ,5 => false ,6 => true ,7 => false ,8 => true  ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][5][4]['possible'] = array(1 => false ,2 => false ,3 => false,4 => false ,5 => false ,6 => false ,7 => true  ,8 => false ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][5][5]['possible'] = array(1 => false ,2 => true  ,3 => false,4 => false ,5 => false  ,6 => false ,7 => false  ,8 => true  ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][5][6]['possible'] = array(1 => false ,2 => false ,3 => false,4 => false ,5 => true  ,6 => false ,7 => false ,8 => false ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][5][7]['possible'] = array(1 => false ,2 => true  ,3 => false,4 => false ,5 => false ,6 => true ,7 => true  ,8 => false ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][5][8]['possible'] = array_fill(1,9,false);
        $_SESSION['pSudokuHelper']['sudoku'][5][9]['possible'] = array_fill(1,9,false);
        
        $_SESSION['pSudokuHelper']['sudoku'][6][1]['possible'] = array_fill(1,9,false);
        $_SESSION['pSudokuHelper']['sudoku'][6][2]['possible'] = array_fill(1,9,false);
        $_SESSION['pSudokuHelper']['sudoku'][6][3]['possible'] = array(1 => false ,2 => false ,3 => false,4 => true  ,5 => false ,6 => false,7 => false ,8 => true  ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][6][4]['possible'] = array(1 => false ,2 => false ,3 => false ,4 => false ,5 => false ,6 => true ,7 => false ,8 => false ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][6][5]['possible'] = array(1 => false ,2 => false ,3 => false,4 => true  ,5 => false ,6 => false,7 => false ,8 => true  ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][6][6]['possible'] = array_fill(1,9,false);
        $_SESSION['pSudokuHelper']['sudoku'][6][7]['possible'] = array(1 => false ,2 => true  ,3 => true ,4 => false ,5 => false ,6 => true ,7 => false ,8 => false ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][6][8]['possible'] = array(1 => false ,2 => true  ,3 => false,4 => false ,5 => true  ,6 => true ,7 => false ,8 => false ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][6][9]['possible'] = array(1 => false ,2 => false ,3 => true ,4 => false ,5 => true  ,6 => false,7 => false ,8 => false ,9 => false ) ;
        
        $_SESSION['pSudokuHelper']['sudoku'][7][1]['possible'] = array(1 => false ,2 => false ,3 => true ,4 => false ,5 => true  ,6 => false,7 => false ,8 => false ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][7][2]['possible'] = array_fill(1,9,false);
        $_SESSION['pSudokuHelper']['sudoku'][7][3]['possible'] = array(1 => false ,2 => false ,3 => true ,4 => false ,5 => false ,6 => false,7 => true  ,8 => false  ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][7][4]['possible'] = array_fill(1,9,false);
        $_SESSION['pSudokuHelper']['sudoku'][7][5]['possible'] = array(1 => false ,2 => false ,3 => true ,4 => false ,5 => true  ,6 => false,7 => true  ,8 => false ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][7][6]['possible'] = array_fill(1,9,false);
        $_SESSION['pSudokuHelper']['sudoku'][7][7]['possible'] = array_fill(1,9,false);
        $_SESSION['pSudokuHelper']['sudoku'][7][8]['possible'] = array_fill(1,9,false);
        $_SESSION['pSudokuHelper']['sudoku'][7][9]['possible'] = array_fill(1,9,false);
        
        $_SESSION['pSudokuHelper']['sudoku'][8][1]['possible'] = array(1 => true  ,2 => false ,3 => true ,4 => true  ,5 => true  ,6 => true ,7 => false ,8 => false ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][8][2]['possible'] = array(1 => false ,2 => false ,3 => false,4 => true  ,5 => true  ,6 => true ,7 => false ,8 => false  ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][8][3]['possible'] = array(1 => true  ,2 => false ,3 => true ,4 => true  ,5 => false ,6 => true ,7 => true  ,8 => false  ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][8][4]['possible'] = array(1 => false ,2 => false ,3 => true ,4 => false ,5 => false ,6 => false ,7 => false  ,8 => false ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][8][5]['possible'] = array(1 => false ,2 => false ,3 => true ,4 => false ,5 => true  ,6 => true ,7 => true  ,8 => false ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][8][6]['possible'] = array_fill(1,9,false);
        $_SESSION['pSudokuHelper']['sudoku'][8][7]['possible'] = array(1 => false ,2 => true  ,3 => false,4 => true  ,5 => false ,6 => false,7 => true  ,8 => true  ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][8][8]['possible'] = array(1 => false ,2 => true  ,3 => false,4 => false ,5 => false ,6 => false,7 => true  ,8 => false ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][8][9]['possible'] = array(1 => false ,2 => false ,3 => false,4 => true  ,5 => false ,6 => false,7 => true  ,8 => true  ,9 => false ) ;
        
        $_SESSION['pSudokuHelper']['sudoku'][9][1]['possible'] = array_fill(1,9,false);
        $_SESSION['pSudokuHelper']['sudoku'][9][2]['possible'] = array(1 => false ,2 => true  ,3 => false,4 => true  ,5 => false ,6 => true ,7 => false ,8 => false ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][9][3]['possible'] = array(1 => false ,2 => true  ,3 => false,4 => true  ,5 => false ,6 => true ,7 => true  ,8 => false ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][9][4]['possible'] = array_fill(1,9,false);
        $_SESSION['pSudokuHelper']['sudoku'][9][5]['possible'] = array(1 => false ,2 => false ,3 => false,4 => false ,5 => false ,6 => true ,7 => true  ,8 => false ,9 => false ) ;
        $_SESSION['pSudokuHelper']['sudoku'][9][6]['possible'] = array_fill(1,9,false);
        $_SESSION['pSudokuHelper']['sudoku'][9][7]['possible'] = array_fill(1,9,false);
        $_SESSION['pSudokuHelper']['sudoku'][9][8]['possible'] = array_fill(1,9,false);
        $_SESSION['pSudokuHelper']['sudoku'][9][9]['possible'] = array(1 => false ,2 => false ,3 => false,4 => true  ,5 => false ,6 => false,7 => true  ,8 => false ,9 => false ) ;
        break;
        
    case 'backup':
        $_SESSION['pSudokuHelper']['backup'][DATETIME_NOW] = $_SESSION['pSudokuHelper']['sudoku'];
        break;
        
    case 'restore':
        $_SESSION['pSudokuHelper']['sudoku'] = $_SESSION['pSudokuHelper']['backup'][$getId];
        break;
}    	

admRedirect(ADMIDIO_URL . FOLDER_PLUGINS . PLUGIN_FOLDER. '/sudokuhelper.php');
