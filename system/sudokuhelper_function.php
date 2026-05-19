<?php
/**
 ***********************************************************************************************
 * Verarbeiten der Einstellungen des Admidio-Plugins SudokuHelper
 *
 * @copyright rmb
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2.0 only
 ***********************************************************************************************
 */

/**
 * ****************************************************************************
 * Parameters:
 *
 * mode : find_equals - rule find numbers
 * set - set an pattern sudoku (only for testing)
 * backup - save the game
 * restore - restore the game
 * stepback - step back
 * cannotbe_mustnotbe - rule cant´t be/must not be
 * save_single - save single values
 *
 * anz : number for find_equals
 *
 * id : id of the game to restore
 *
 * ***************************************************************************
 */
require_once (__DIR__ . '/../../../system/common.php');
require_once (__DIR__ . '/common_function.php');

// Initialize and check the parameters
$getMode = admFuncVariableIsValid($_GET, 'mode', 'string');
$getAnz = admFuncVariableIsValid($_GET, 'anz', 'numeric');
$getId = admFuncVariableIsValid($_GET, 'id', 'string');

switch ($getMode) {
    case 'find_equals':

        $_SESSION['pSudokuHelper']['previous'] = $_SESSION['pSudokuHelper']['sudoku'];

        // Zeilen und Spalten prüfen
        // wird horizontal geprüft, dann wird $koord1 durch $row und $koord2 durch $col ersetzt
        // wird vertikal geprüft, dann wird $koord1 durch $col und $koord2 durch $row ersetzt
        $rowToCol = true;
        $updateRequired = false;
        for ($i = 1; $i < 3; $i ++) // for-Schleife wird 2x durchlaufen, einmal rowToCol, einmal colToRow
        {
            for ($koord1 = 1; $koord1 < 10; $koord1 ++) {
                $arbeitsarray = array();
                $found = array();
                for ($koord2 = 1; $koord2 < 10; $koord2 ++) {
                    $arbeitsarray[$koord2] = $_SESSION['pSudokuHelper']['sudoku'][($rowToCol ? $koord1 : $koord2)][($rowToCol ? $koord2 : $koord1)]['possible'];
                }

                $found = search_numbers($getAnz, $arbeitsarray);

                if (sizeof($found) > 0) {
                    foreach ($found as $key => $data) {
                        for ($koord2 = 1; $koord2 < 10; $koord2 ++) {
                            $result = array_intersect($data, array_keys($_SESSION['pSudokuHelper']['sudoku'][($rowToCol ? $koord1 : $koord2)][($rowToCol ? $koord2 : $koord1)]['possible'], true));

                            if (sizeof($result) > 0) // ja, in dieser Spalte gibt es eine Übereinstimmung
                            {
                                $updateRequired = true;
                                $_SESSION['pSudokuHelper']['sudoku'][($rowToCol ? $koord1 : $koord2)][($rowToCol ? $koord2 : $koord1)]['possible'] = array_fill(1, 9, false);
                                foreach ($data as $datakey) {
                                    $_SESSION['pSudokuHelper']['sudoku'][($rowToCol ? $koord1 : $koord2)][($rowToCol ? $koord2 : $koord1)]['possible'][$datakey] = true;
                                }
                            }
                        }
                    }
                }
            }
            $rowToCol = false; // jetzt Durchlauf colToRow
        }

        // 9er Block prüfen
        $neunerBlock = array();
        $neunerBlock['ol'] = array(
            'row' => 1,
            'col' => 1
        ); // ol = oben links
        $neunerBlock['om'] = array(
            'row' => 1,
            'col' => 4
        ); // om = oben mitte
        $neunerBlock['or'] = array(
            'row' => 1,
            'col' => 7
        ); // or = oben rechts
        $neunerBlock['ml'] = array(
            'row' => 4,
            'col' => 1
        ); // ml = mitte links
        $neunerBlock['mm'] = array(
            'row' => 4,
            'col' => 4
        ); // mm = mitte mitte
        $neunerBlock['mr'] = array(
            'row' => 4,
            'col' => 7
        ); // mr = mitte rechts
        $neunerBlock['ul'] = array(
            'row' => 7,
            'col' => 1
        ); // ul = unten links
        $neunerBlock['um'] = array(
            'row' => 7,
            'col' => 4
        ); // um = unten mitte
        $neunerBlock['ur'] = array(
            'row' => 7,
            'col' => 7
        ); // ur = unten rechts

        foreach ($neunerBlock as $block => $blockData) {
            $arbeitsarray = array();
            $found = array();
            $i = 1;

            for ($row = $blockData['row']; $row < $blockData['row'] + 3; $row ++) {
                for ($col = $blockData['col']; $col < $blockData['col'] + 3; $col ++) {
                    $arbeitsarray[$i] = $_SESSION['pSudokuHelper']['sudoku'][$row][$col]['possible'];
                    $i ++;
                }
            }
            $found = search_numbers($getAnz, $arbeitsarray);

            if (sizeof($found) > 0) {
                foreach ($found as $key => $data) {
                    for ($row = $blockData['row']; $row < $blockData['row'] + 3; $row ++) {
                        for ($col = $blockData['col']; $col < $blockData['col'] + 3; $col ++) {
                            $result = array_intersect($data, array_keys($_SESSION['pSudokuHelper']['sudoku'][$row][$col]['possible'], true));

                            if (sizeof($result) > 0) // ja, in diesem Block gibt es eine Übereinstimmung
                            {
                                $updateRequired = true;
                                $_SESSION['pSudokuHelper']['sudoku'][$row][$col]['possible'] = array_fill(1, 9, false);
                                foreach ($data as $datakey) {
                                    $_SESSION['pSudokuHelper']['sudoku'][$row][$col]['possible'][$datakey] = true;
                                }
                            }
                        }
                    }
                }
            }
        }
        if ($updateRequired) {

            updateStepback();
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

        $_SESSION['pSudokuHelper']['sudoku'][1][1]['possible'] = array(
            1 => false,
            2 => false,
            3 => true,
            4 => true,
            5 => false,
            6 => true,
            7 => false,
            8 => false,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][1][2]['possible'] = array_fill(1, 9, false);
        $_SESSION['pSudokuHelper']['sudoku'][1][3]['possible'] = array_fill(1, 9, false);
        $_SESSION['pSudokuHelper']['sudoku'][1][4]['possible'] = array_fill(1, 9, false);
        $_SESSION['pSudokuHelper']['sudoku'][1][5]['possible'] = array(
            1 => false,
            2 => false,
            3 => true,
            4 => false,
            5 => false,
            6 => true,
            7 => false,
            8 => false,
            9 => true
        );
        $_SESSION['pSudokuHelper']['sudoku'][1][6]['possible'] = array_fill(1, 9, false);
        $_SESSION['pSudokuHelper']['sudoku'][1][7]['possible'] = array(
            1 => false,
            2 => false,
            3 => true,
            4 => true,
            5 => false,
            6 => true,
            7 => false,
            8 => false,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][1][8]['possible'] = array(
            1 => false,
            2 => false,
            3 => false,
            4 => false,
            5 => false,
            6 => true,
            7 => false,
            8 => false,
            9 => true
        );
        $_SESSION['pSudokuHelper']['sudoku'][1][9]['possible'] = array_fill(1, 9, false);

        $_SESSION['pSudokuHelper']['sudoku'][2][1]['possible'] = array(
            1 => false,
            2 => true,
            3 => true,
            4 => false,
            5 => false,
            6 => true,
            7 => false,
            8 => false,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][2][2]['possible'] = array(
            1 => false,
            2 => true,
            3 => false,
            4 => false,
            5 => false,
            6 => true,
            7 => false,
            8 => false,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][2][3]['possible'] = array(
            1 => false,
            2 => true,
            3 => true,
            4 => false,
            5 => false,
            6 => true,
            7 => false,
            8 => false,
            9 => true
        );
        $_SESSION['pSudokuHelper']['sudoku'][2][4]['possible'] = array_fill(1, 9, false);
        $_SESSION['pSudokuHelper']['sudoku'][2][5]['possible'] = array(
            1 => true,
            2 => false,
            3 => true,
            4 => false,
            5 => false,
            6 => true,
            7 => false,
            8 => false,
            9 => true
        );
        $_SESSION['pSudokuHelper']['sudoku'][2][6]['possible'] = array(
            1 => false,
            2 => false,
            3 => false,
            4 => false,
            5 => false,
            6 => true,
            7 => false,
            8 => false,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][2][7]['possible'] = array(
            1 => true,
            2 => false,
            3 => true,
            4 => false,
            5 => false,
            6 => true,
            7 => true,
            8 => true,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][2][8]['possible'] = array(
            1 => false,
            2 => false,
            3 => false,
            4 => false,
            5 => true,
            6 => true,
            7 => true,
            8 => false,
            9 => true
        );
        $_SESSION['pSudokuHelper']['sudoku'][2][9]['possible'] = array(
            1 => false,
            2 => false,
            3 => true,
            4 => false,
            5 => true,
            6 => false,
            7 => true,
            8 => true,
            9 => false
        );

        $_SESSION['pSudokuHelper']['sudoku'][3][1]['possible'] = array_fill(1, 9, false);
        $_SESSION['pSudokuHelper']['sudoku'][3][2]['possible'] = array_fill(1, 9, false);
        $_SESSION['pSudokuHelper']['sudoku'][3][3]['possible'] = array(
            1 => false,
            2 => false,
            3 => true,
            4 => true,
            5 => false,
            6 => true,
            7 => false,
            8 => false,
            9 => true
        );
        $_SESSION['pSudokuHelper']['sudoku'][3][4]['possible'] = array_fill(1, 9, false);
        $_SESSION['pSudokuHelper']['sudoku'][3][5]['possible'] = array(
            1 => true,
            2 => false,
            3 => true,
            4 => false,
            5 => false,
            6 => true,
            7 => false,
            8 => false,
            9 => true
        );
        $_SESSION['pSudokuHelper']['sudoku'][3][6]['possible'] = array_fill(1, 9, false);
        $_SESSION['pSudokuHelper']['sudoku'][3][7]['possible'] = array(
            1 => true,
            2 => false,
            3 => true,
            4 => true,
            5 => false,
            6 => true,
            7 => false,
            8 => false,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][3][8]['possible'] = array(
            1 => false,
            2 => false,
            3 => false,
            4 => false,
            5 => false,
            6 => true,
            7 => false,
            8 => false,
            9 => true
        );
        $_SESSION['pSudokuHelper']['sudoku'][3][9]['possible'] = array(
            1 => false,
            2 => false,
            3 => true,
            4 => true,
            5 => false,
            6 => false,
            7 => false,
            8 => false,
            9 => false
        );

        $_SESSION['pSudokuHelper']['sudoku'][4][1]['possible'] = array(
            1 => false,
            2 => true,
            3 => false,
            4 => true,
            5 => true,
            6 => true,
            7 => false,
            8 => false,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][4][2]['possible'] = array(
            1 => false,
            2 => true,
            3 => false,
            4 => true,
            5 => true,
            6 => true,
            7 => false,
            8 => false,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][4][3]['possible'] = array(
            1 => false,
            2 => true,
            3 => false,
            4 => true,
            5 => false,
            6 => true,
            7 => false,
            8 => false,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][4][4]['possible'] = array_fill(1, 9, false);
        $_SESSION['pSudokuHelper']['sudoku'][4][5]['possible'] = array(
            1 => false,
            2 => true,
            3 => false,
            4 => true,
            5 => false,
            6 => false,
            7 => false,
            8 => false,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][4][6]['possible'] = array(
            1 => false,
            2 => false,
            3 => true,
            4 => false,
            5 => false,
            6 => false,
            7 => false,
            8 => false,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][4][7]['possible'] = array(
            1 => false,
            2 => false,
            3 => false,
            4 => false,
            5 => false,
            6 => false,
            7 => true,
            8 => false,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][4][8]['possible'] = array_fill(1, 9, false);
        $_SESSION['pSudokuHelper']['sudoku'][4][9]['possible'] = array_fill(1, 9, false);

        $_SESSION['pSudokuHelper']['sudoku'][5][1]['possible'] = array(
            1 => true,
            2 => true,
            3 => false,
            4 => false,
            5 => true,
            6 => true,
            7 => false,
            8 => false,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][5][2]['possible'] = array_fill(1, 9, false);
        $_SESSION['pSudokuHelper']['sudoku'][5][3]['possible'] = array(
            1 => true,
            2 => true,
            3 => false,
            4 => false,
            5 => false,
            6 => true,
            7 => false,
            8 => true,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][5][4]['possible'] = array(
            1 => false,
            2 => false,
            3 => false,
            4 => false,
            5 => false,
            6 => false,
            7 => true,
            8 => false,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][5][5]['possible'] = array(
            1 => false,
            2 => true,
            3 => false,
            4 => false,
            5 => false,
            6 => false,
            7 => false,
            8 => true,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][5][6]['possible'] = array(
            1 => false,
            2 => false,
            3 => false,
            4 => false,
            5 => true,
            6 => false,
            7 => false,
            8 => false,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][5][7]['possible'] = array(
            1 => false,
            2 => true,
            3 => false,
            4 => false,
            5 => false,
            6 => true,
            7 => true,
            8 => false,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][5][8]['possible'] = array_fill(1, 9, false);
        $_SESSION['pSudokuHelper']['sudoku'][5][9]['possible'] = array_fill(1, 9, false);

        $_SESSION['pSudokuHelper']['sudoku'][6][1]['possible'] = array_fill(1, 9, false);
        $_SESSION['pSudokuHelper']['sudoku'][6][2]['possible'] = array_fill(1, 9, false);
        $_SESSION['pSudokuHelper']['sudoku'][6][3]['possible'] = array(
            1 => false,
            2 => false,
            3 => false,
            4 => true,
            5 => false,
            6 => false,
            7 => false,
            8 => true,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][6][4]['possible'] = array(
            1 => false,
            2 => false,
            3 => false,
            4 => false,
            5 => false,
            6 => true,
            7 => false,
            8 => false,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][6][5]['possible'] = array(
            1 => false,
            2 => false,
            3 => false,
            4 => true,
            5 => false,
            6 => false,
            7 => false,
            8 => true,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][6][6]['possible'] = array_fill(1, 9, false);
        $_SESSION['pSudokuHelper']['sudoku'][6][7]['possible'] = array(
            1 => false,
            2 => true,
            3 => true,
            4 => false,
            5 => false,
            6 => true,
            7 => false,
            8 => false,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][6][8]['possible'] = array(
            1 => false,
            2 => true,
            3 => false,
            4 => false,
            5 => true,
            6 => true,
            7 => false,
            8 => false,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][6][9]['possible'] = array(
            1 => false,
            2 => false,
            3 => true,
            4 => false,
            5 => true,
            6 => false,
            7 => false,
            8 => false,
            9 => false
        );

        $_SESSION['pSudokuHelper']['sudoku'][7][1]['possible'] = array(
            1 => false,
            2 => false,
            3 => true,
            4 => false,
            5 => true,
            6 => false,
            7 => false,
            8 => false,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][7][2]['possible'] = array_fill(1, 9, false);
        $_SESSION['pSudokuHelper']['sudoku'][7][3]['possible'] = array(
            1 => false,
            2 => false,
            3 => true,
            4 => false,
            5 => false,
            6 => false,
            7 => true,
            8 => false,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][7][4]['possible'] = array_fill(1, 9, false);
        $_SESSION['pSudokuHelper']['sudoku'][7][5]['possible'] = array(
            1 => false,
            2 => false,
            3 => true,
            4 => false,
            5 => true,
            6 => false,
            7 => true,
            8 => false,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][7][6]['possible'] = array_fill(1, 9, false);
        $_SESSION['pSudokuHelper']['sudoku'][7][7]['possible'] = array_fill(1, 9, false);
        $_SESSION['pSudokuHelper']['sudoku'][7][8]['possible'] = array_fill(1, 9, false);
        $_SESSION['pSudokuHelper']['sudoku'][7][9]['possible'] = array_fill(1, 9, false);

        $_SESSION['pSudokuHelper']['sudoku'][8][1]['possible'] = array(
            1 => true,
            2 => false,
            3 => true,
            4 => true,
            5 => true,
            6 => true,
            7 => false,
            8 => false,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][8][2]['possible'] = array(
            1 => false,
            2 => false,
            3 => false,
            4 => true,
            5 => true,
            6 => true,
            7 => false,
            8 => false,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][8][3]['possible'] = array(
            1 => true,
            2 => false,
            3 => true,
            4 => true,
            5 => false,
            6 => true,
            7 => true,
            8 => false,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][8][4]['possible'] = array(
            1 => false,
            2 => false,
            3 => true,
            4 => false,
            5 => false,
            6 => false,
            7 => false,
            8 => false,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][8][5]['possible'] = array(
            1 => false,
            2 => false,
            3 => true,
            4 => false,
            5 => true,
            6 => true,
            7 => true,
            8 => false,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][8][6]['possible'] = array_fill(1, 9, false);
        $_SESSION['pSudokuHelper']['sudoku'][8][7]['possible'] = array(
            1 => false,
            2 => true,
            3 => false,
            4 => true,
            5 => false,
            6 => false,
            7 => true,
            8 => true,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][8][8]['possible'] = array(
            1 => false,
            2 => true,
            3 => false,
            4 => false,
            5 => false,
            6 => false,
            7 => true,
            8 => false,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][8][9]['possible'] = array(
            1 => false,
            2 => false,
            3 => false,
            4 => true,
            5 => false,
            6 => false,
            7 => true,
            8 => true,
            9 => false
        );

        $_SESSION['pSudokuHelper']['sudoku'][9][1]['possible'] = array_fill(1, 9, false);
        $_SESSION['pSudokuHelper']['sudoku'][9][2]['possible'] = array(
            1 => false,
            2 => true,
            3 => false,
            4 => true,
            5 => false,
            6 => true,
            7 => false,
            8 => false,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][9][3]['possible'] = array(
            1 => false,
            2 => true,
            3 => false,
            4 => true,
            5 => false,
            6 => true,
            7 => true,
            8 => false,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][9][4]['possible'] = array_fill(1, 9, false);
        $_SESSION['pSudokuHelper']['sudoku'][9][5]['possible'] = array(
            1 => false,
            2 => false,
            3 => false,
            4 => false,
            5 => false,
            6 => true,
            7 => true,
            8 => false,
            9 => false
        );
        $_SESSION['pSudokuHelper']['sudoku'][9][6]['possible'] = array_fill(1, 9, false);
        $_SESSION['pSudokuHelper']['sudoku'][9][7]['possible'] = array_fill(1, 9, false);
        $_SESSION['pSudokuHelper']['sudoku'][9][8]['possible'] = array_fill(1, 9, false);
        $_SESSION['pSudokuHelper']['sudoku'][9][9]['possible'] = array(
            1 => false,
            2 => false,
            3 => false,
            4 => true,
            5 => false,
            6 => false,
            7 => true,
            8 => false,
            9 => false
        );
        updateStepback();
        break;

    case 'backup':
        $_SESSION['pSudokuHelper']['backup'][DATETIME_NOW] = $_SESSION['pSudokuHelper']['sudoku'];
        break;

    case 'restore':
        $_SESSION['pSudokuHelper']['sudoku'] = $_SESSION['pSudokuHelper']['backup'][$getId];
        $_SESSION['pSudokuHelper']['stepback'] = array();
        updateStepback();
        break;

    case 'stepback':
        $compareArr = array_pop($_SESSION['pSudokuHelper']['stepback']);
        if ($_SESSION['pSudokuHelper']['sudoku'] === $compareArr) {
            $_SESSION['pSudokuHelper']['sudoku'] = array_pop($_SESSION['pSudokuHelper']['stepback']);
        } else {
            $_SESSION['pSudokuHelper']['sudoku'] = $compareArr;
        }
        break;

    case 'cannotbe_mustnotbe':
        
        /*
         * Regel:
         * Betrachtet werden immer 3 quadratische, nebeneinander liegende 9er Blöcke
         * Bei waagrechter Prüfung: Aufgeteilt in drei Zeilen ($startrow, $startrow+1 und $startrow +2)
         * In einen 9er Block in einer Zeile ist der aus drei nebeneinander liegenden Kästchen bestehende "Gegeben-Block". In diesem Block ist der zu prüfende Wert x/y ($numberSet)
         * Bei senkrechter Prüfung: entsprechend
         * Geprüft wird auf "Blöcke" (= drei nebeneinander liegenden Kästchen, ->> befindet sich ein Wert in einem Block?...)
         * Die gesamte Zeile mit dem "Gegeben-Block" wird bei der weiteren Prüfung nicht betrachtet
         * Die drei Spalten, in denen sich der "Gegeben-Block" befindet, werden bei der weiteren Prüfung auch nicht betrachtet
         * In den verbliebenen zwei Zeilen gibt es jetzt 4 Dreier-Blöcke
         * Wenn in einem dieser vier 3er-Blöcke der Wert aus x/y in keinen Possible-Daten enthalten ist, dann ist das der "Hier kanns nicht sein-Block"
         * Wenn es einen "Hier kanns nicht sein-Block" gibt, gibt es auch einen komplementär (gegenüber liegend) vorhandenen "Hier darfs nicht sein-Block"
         * Jetzt alle x/y-Werte ($numberSet) in den Possible-Daten im "Hier darfs nicht sein-Block" löschen
         */
        
        $_SESSION['pSudokuHelper']['previous'] = $_SESSION['pSudokuHelper']['sudoku'];

        // alle Zeilen durchlaufen
        for ($row = 1; $row < 10; $row ++) {

            // alle Spalten durchlaufen
            for ($col = 1; $col < 10; $col ++) {

                // nur weiter, wenn für dieses Feld ein Wert (eine Zahl 1-9) gesetzt wurde
                if ($_SESSION['pSudokuHelper']['sudoku'][$row][$col]['set'] === 0) {
                    continue;
                } else {
                    // den in diesem Feld gesetzten Wert auslesen
                    $numberSet = (int) $_SESSION['pSudokuHelper']['sudoku'][$row][$col]['set'];

                    // in der Zeile $row befindet sich der der einzulesende bzw. zu prüfende Wert - die Startzeile für diesen "Gegeben-Block" bestimmen
                    $startRow = getStartRowOrCol($row);
                    // in der Spalte $col befindet sich der einzulesende bzw. zu prüfende Wert - die Startspalte für diesen "Gegeben-Block" bestimmen
                    $startCol = getStartRowOrCol($col);

                    // ab hier die Unterscheidung, ob waagrecht oder senkrecht geprüft wird
                    // ich habe 3 Möglichkeiten gefunden, das zu programmieren:
                    // 1. im nachfolgenden Code wird $trow und $tcol durch Variablen ersetzt - Vorteil: kurzer Code - Nachteil: sehr unübersichtlich
                    // 2. der Code bleibt gleich, aber das ganze Sudoku wird quasi um 90 Grad gedreht. Position 1.1 wird 1.9 usw - Vorteil: kurzer Code - Nachteil: alle Daten der $_SESSION müssen temporär geändert werden
                    // 3. ein weiterer Codeabschnitt zur Prüfung senkrecht wird eingebaut - Vorteil: übersichtlich - Nachteil: der Code wird doppelt so lang
                    // Option 3 wird realisiert

                    // waagrechte Prüfung

                    $workArray = array();

                    // das Arbeitsarray mit Daten befüllen (ohne die Zeilen und Spalten mit dem "Gegeben-Block")

                    for ($trow = 1; $trow < 10; $trow ++) {
                        if ($row != $trow && getStartRowOrCol($trow) === $startRow) {
                            for ($tcol = 1; $tcol < 10; $tcol ++) {
                                if (getStartRowOrCol($tcol) != $startCol) {
                                    $workArray[$trow]['set'][$tcol] = $_SESSION['pSudokuHelper']['sudoku'][$trow][$tcol]['set'];
                                    $workArray[$trow]['possible'][$tcol] = $_SESSION['pSudokuHelper']['sudoku'][$trow][$tcol]['possible'];
                                }
                            }
                        }
                    }

                    // ist der zu prüfende Wert in einer Zeile bereits vorhanden, dann diese Zeile löschen (der Wert darf weder in row2 noch in row3 als set gespeichert sein)
                    foreach ($workArray as $trow => $data) {
                        if (in_array($numberSet, $data['set'])) {
                            unset($workArray[$trow]);
                        }
                    }

                    // weiter nur, wenn noch genau zwei für die Prüfung vorhandene Zeilen vorhanden sind
                    if (count($workArray) === 2) {

                        // jetzt prüfen, ob der Wert aus x/y in einem der 4 3er-Blöcke NICHT in den Possible-daten enthalten ist. Dieser Block wäre dann der "Hier kanns nicht sein-Block"
                        $checkArray = array();
                        $foundInRow = 0;
                        $foundInColBlock = 0;

                        foreach ($workArray as $trow => $data) {

                            foreach ($data['possible'] as $tcol => $datapos) {
                                $workBlock = getStartRowOrCol($tcol);
                                if (! $datapos[$numberSet]) {
                                    if (! isset($checkArray[$trow][$workBlock])) {
                                        $checkArray[$trow][$workBlock] = 1;
                                    } else {
                                        $checkArray[$trow][$workBlock] ++;

                                        // beim ersten Auftreten von 3x Vorhanden-Sein des Wertes die Schleife verlassen (weitere Prüfung nicht erforderlich, das der "Hier kanns nicht sein-Block" gefunden wurde)
                                        if ($checkArray[$trow][$workBlock] === 3) {
                                            $foundInRow = $trow;
                                            $foundInColBlock = $workBlock;
                                            break;
                                        }
                                    }
                                }
                            }
                        }

                        // wenn der "Hier kanns nicht sein-Block" gefunden wurde, dann in den Possible-Daten des "Hier kanns nicht sein-Block" den Wert aus x/y ($numberSet) auf false setzen.
                        if ($foundInRow != 0) {
                            foreach ($workArray as $trow => $data) {
                                if ($trow === $foundInRow) {
                                    continue;
                                }
                                foreach ($data['possible'] as $tcol => $datapos) {
                                    if ((getStartRowOrCol($tcol)) === $foundInColBlock) {
                                        continue;
                                    }
                                    $_SESSION['pSudokuHelper']['sudoku'][$trow][$tcol]['possible'][$numberSet] = false;
                                }
                            }
                        }
                    }

                    // senkrechte Prüfung

                    $workArray = array();

                    // das Arbeitsarray mit Daten befüllen (ohne die Zeilen und Spalten mit dem "Gegeben-Block")

                    for ($tcol = 1; $tcol < 10; $tcol ++) {
                        if ($col != $tcol && getStartRowOrCol($tcol) === $startCol) {
                            for ($trow = 1; $trow < 10; $trow ++) {
                                if (getStartRowOrCol($trow) != $startRow) {
                                    $workArray[$tcol]['set'][$trow] = $_SESSION['pSudokuHelper']['sudoku'][$trow][$tcol]['set'];
                                    $workArray[$tcol]['possible'][$trow] = $_SESSION['pSudokuHelper']['sudoku'][$trow][$tcol]['possible'];
                                }
                            }
                        }
                    }

                    // ist der zu prüfende Wert in einer Zeile bereits vorhanden, dann diese Zeile löschen (der Wert darf weder in row2 noch in row3 als set gespeichert sein)
                    foreach ($workArray as $tcol => $data) {
                        if (in_array($numberSet, $data['set'])) {
                            unset($workArray[$tcol]);
                        }
                    }

                    // weiter nur, wenn noch genau zwei für die Prüfung vorhandene Zeilen vorhanden sind
                    if (count($workArray) === 2) {

                        // jetzt prüfen, ob der Wert aus x/y in einem der 4 3er-Blöcke NICHT in den Possible-daten enthalten ist. Dieser Block wäre dann der "Hier kanns nicht sein-Block"
                        $checkArray = array();
                        $foundInCol = 0;
                        $foundInRowBlock = 0;

                        foreach ($workArray as $tcol => $data) {

                            foreach ($data['possible'] as $trow => $datapos) {
                                $workBlock = getStartRowOrCol($trow);
                                if (! $datapos[$numberSet]) {
                                    if (! isset($checkArray[$tcol][$workBlock])) {
                                        $checkArray[$tcol][$workBlock] = 1;
                                    } else {
                                        $checkArray[$tcol][$workBlock] ++;

                                        // beim ersten Auftreten von 3x Vorhanden-Sein des Wertes die Schleife verlassen (weitere Prüfung nicht erforderlich, das der "Hier kanns nicht sein-Block" gefunden wurde)
                                        if ($checkArray[$tcol][$workBlock] === 3) {
                                            $foundInCol = $tcol;
                                            $foundInRowBlock = $workBlock;
                                            break;
                                        }
                                    }
                                }
                            }
                        }

                        // wenn der "Hier kanns nicht sein-Block" gefunden wurde, in den Possible-Daten des "Hier kanns nicht sein-Block" den Wert aus x/y ($numberSet) auf false setzen.
                        if ($foundInCol != 0) {
                            foreach ($workArray as $tcol => $data) {
                                if ($tcol === $foundInCol) {
                                    continue;
                                }
                                foreach ($data['possible'] as $trow => $datapos) {
                                    if ((getStartRowOrCol($trow)) === $foundInRowBlock) {
                                        continue;
                                    }
                                    $_SESSION['pSudokuHelper']['sudoku'][$trow][$tcol]['possible'][$numberSet] = false;
                                }
                            }
                        }
                    }
                } // ende else schleife
            }
        }

        updateStepback();

        break;

    case 'save_single':

        $_SESSION['pSudokuHelper']['previous'] = $_SESSION['pSudokuHelper']['sudoku'];
        for ($row = 1; $row < 10; $row ++) {

            for ($col = 1; $col < 10; $col ++) {

                $count = 0;
                foreach ($_SESSION['pSudokuHelper']['sudoku'][$row][$col]['possible'] as $val) {
                    if ($val) {
                        $count ++;
                    }
                }
                if ($count === 1) {

                    $numberSet = array_search('true', $_SESSION['pSudokuHelper']['sudoku'][$row][$col]['possible']);
                    $_SESSION['pSudokuHelper']['sudoku'][$row][$col]['set'] = $numberSet;
                    $_SESSION['pSudokuHelper']['sudoku'][$row][$col]['possible'] = array_fill(1, 9, false);

                    for ($trow = 1; $trow < 10; $trow ++) {
                        $_SESSION['pSudokuHelper']['sudoku'][$trow][$col]['possible'][$numberSet] = false;
                    }

                    for ($tcol = 1; $tcol < 10; $tcol ++) {
                        $_SESSION['pSudokuHelper']['sudoku'][$row][$tcol]['possible'][$numberSet] = false;
                    }

                    for ($trow = novum($row); $trow < novum($row) + 3; $trow ++) {
                        for ($tcol = novum($col); $tcol < novum($col) + 3; $tcol ++) {
                            $_SESSION['pSudokuHelper']['sudoku'][$trow][$tcol]['possible'][$numberSet] = false;
                        }
                    }
                }
            }
        }

        updateStepback();
        break;
}

admRedirect(ADMIDIO_URL . FOLDER_PLUGINS . PLUGIN_FOLDER . '/system/sudokuhelper.php');
