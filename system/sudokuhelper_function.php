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
 * sample1 - set an pattern sudoku (only for testing)
 * sample2 - set an pattern sudoku (only for testing)
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

        updatePrevious();

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
                                setPossible(($rowToCol ? $koord1 : $koord2), ($rowToCol ? $koord2 : $koord1), 0, false);
                                foreach ($data as $datakey) {
                                    setPossible(($rowToCol ? $koord1 : $koord2), ($rowToCol ? $koord2 : $koord1), $datakey, true);
                                }
                            }
                        }
                    }
                }
            }
            $rowToCol = false; // jetzt Durchlauf colToRow
        }

        // 9er Block (=Unterquadrat) prüfen
        $neunerBlock = generate_neunerblock();

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
                                setPossible($row, $col, 0, false);
                                foreach ($data as $datakey) {
                                    setPossible($row, $col, $datakey, true);
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

    case 'sample1':

        initSudoku();

        setNumber(1, 2, 1);
        setNumber(1, 3, 5);
        setNumber(1, 4, 8);
        setNumber(1, 6, 7);
        setNumber(1, 9, 2);

        setNumber(2, 4, 4);

        setNumber(3, 1, 8);
        setNumber(3, 2, 7);
        setNumber(3, 4, 5);
        setNumber(3, 6, 2);

        setNumber(4, 4, 9);
        setNumber(4, 8, 8);
        setNumber(4, 9, 1);

        setNumber(5, 2, 3);
        setNumber(5, 8, 4);
        setNumber(5, 9, 9);

        setNumber(6, 1, 7);
        setNumber(6, 2, 9);
        setNumber(6, 6, 1);

        setNumber(7, 2, 8);
        setNumber(7, 4, 2);
        setNumber(7, 6, 4);
        setNumber(7, 7, 9);
        setNumber(7, 8, 1);
        setNumber(7, 9, 6);

        setNumber(8, 6, 9);

        setNumber(9, 1, 9);
        setNumber(9, 4, 1);
        setNumber(9, 6, 8);
        setNumber(9, 7, 5);
        setNumber(9, 8, 3);

        updateStepback();
        break;

    case 'sample2':

        initSudoku();
        setNumber(2, 3, 7);
        setNumber(2, 5, 6);
        setNumber(2, 6, 1);
        setNumber(2, 7, 3);

        setNumber(3, 6, 7);
        setNumber(3, 8, 9);
        setNumber(3, 9, 1);

        setNumber(4, 2, 4);
        setNumber(4, 3, 8);
        setNumber(4, 5, 9);
        setNumber(4, 7, 6);
        setNumber(4, 9, 5);

        setNumber(5, 1, 9);
        setNumber(5, 9, 3);

        setNumber(6, 1, 3);
        setNumber(6, 3, 1);
        setNumber(6, 5, 4);
        setNumber(6, 7, 9);
        setNumber(6, 8, 8);

        setNumber(7, 1, 5);
        setNumber(7, 2, 1);
        setNumber(7, 4, 8);

        setNumber(8, 3, 6);
        setNumber(8, 4, 7);
        setNumber(8, 5, 3);
        setNumber(8, 7, 4);

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
         * 
         * Kann hier nicht sein und darf deshalb dort auch nicht sein
         * 
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
        
        updatePrevious();

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

                        // jetzt prüfen, ob der Wert aus x/y in einem der 4 3er-Blöcke NICHT in den Possible-Daten enthalten ist. Dieser Block wäre dann der "Hier kanns nicht sein-Block"
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
                                    setPossible($trow, $tcol, $numberSet, false);
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
                                    setPossible($trow, $tcol, $numberSet, false);
                                }
                            }
                        }
                    }
                } // ende else schleife
            }
        }

        updateStepback();

        break;

    case 'musthere_cannotbethere':
        
        /*
         * Regel:
         * 
         * Müssen hier sein und können deshalb nicht dort sein
         *          
         * Gegeben sind immer 3 quadratische, nebeneinander liegende 9er Blöcke
         * Betrachtet wird dabei immer eine waagrechte Zeile (die Prüfung der senkrechten Spalten funktioniert entsprechend) 
         * Diese Zeile ist aufgeteilt in 3 Dreier-Blöcke 
         * Wenn zwei der drei Dreier-Blöcke mit Zahlen belegt sind, z.B. 5 9 3   2 1 6, dann müssen im letzen, dritten Dreier-Block die restlichen Zahlen sein. Im Beispiel dann 4,7 und 8.
         * Die Zahlen 4, 7 und 8 müssen in diesem Dreier-Block sein (aus dem quadratischen 9er Block) und können nicht an anderer Stelle des 9er-Blocks sein.
         */
        
        updatePrevious();

        // waagrechte Prüfung

        // alle Zeilen durchlaufen
        for ($row = 1; $row < 10; $row ++) {

            $workArray = array();
            $setArray = array();

            // alle Spalten durchlaufen
            for ($col = 1; $col < 10; $col ++) {

                // um die Daten eines 3er-Blocks gemeinsam abzuspeichern, wird die erste Koordinate (waagrecht, als auch senkrecht) dieses 3er-Blocks bestimmt
                // --> $startCol oder $startRow; möglich sind nur die Zahlen 1, 4 oder 7
                $startCol = getStartRowOrCol($col);

                if (! isset($setArray[$startCol]['sets'])) {
                    $setArray[$startCol]['sets'] = array();
                }
                if (! isset($setArray[$startCol]['counter'])) {
                    $setArray[$startCol]['counter'] = 0;
                }

                // Daten nur einlesen, wenn für dieses Feld ein Wert (eine Zahl 1-9) gesetzt wurde
                if ($_SESSION['pSudokuHelper']['sudoku'][$row][$col]['set'] != 0) {
                    $setArray[$startCol]['sets'][] = (int) $_SESSION['pSudokuHelper']['sudoku'][$row][$col]['set'];
                    $setArray[$startCol]['counter'] ++;
                }
            }

            // nur weiter, wenn die Regel auch angewendet werden kann
            // --> zwei 3er-Blöcke müssen jeweils 3 Zahlen aufweisen (also vollständig ausgefüllt sein)
            // --> im dritten 3er-Block darf höchstens eine Zahl stehen (also höchstens ein Feld belegt sein)
            if (($setArray[1]['counter'] === 3 && $setArray[4]['counter'] === 3 && $setArray[7]['counter'] <= 1)) {
                $startCol = 7;
            } elseif ($setArray[1]['counter'] === 3 && $setArray[7]['counter'] === 3 && $setArray[4]['counter'] <= 1) {
                $startCol = 4;
            } elseif ($setArray[4]['counter'] === 3 && $setArray[7]['counter'] === 3 && $setArray[1]['counter'] <= 1) {
                $startCol = 1;
            } else {
                // zur nächsten Zeile
                continue;
            }

            $workArray = array_diff(array(
                1,
                2,
                3,
                4,
                5,
                6,
                7,
                8,
                9
            ), array_merge($setArray[1]['sets'], $setArray[4]['sets'], $setArray[7]['sets']));

            // in $workArray sind jetzt die Zahlen, die auf die noch freien Stellen müssen
            // sie können aber nur in die freien Stellen gesetzt werden und an keiner anderen Stelle des 9er-Blocks
            // deshalb im 9er-Block alle Möglichkeiten in den Possible-Daten löschen

            $startRow = getStartRowOrCol($row);

            for ($trow = novum($startRow); $trow < novum($startRow) + 3; $trow ++) {
                if ($trow != $row) {
                    for ($tcol = novum($startCol); $tcol < novum($startCol) + 3; $tcol ++) {
                        foreach ($workArray as $numberSet) {
                            setPossible($trow, $tcol, $numberSet, false);
                        }
                    }
                }
            }
        }

        // senkrechte Prüfung

        // alle Spalten durchlaufen
        for ($col = 1; $col < 10; $col ++) {

            $workArray = array();
            $setArray = array();

            // alle Spalten durchlaufen
            for ($row = 1; $row < 10; $row ++) {

                // um die Daten eines 3er-Blocks gemeinsam abzuspeichern, wird die erste Koordinate (waagrecht, als auch senkrecht) dieses 3er-Blocks bestimmt
                // --> $startCol oder $startRow; möglich sind nur die Zahlen 1, 4 oder 7
                $startRow = getStartRowOrCol($row);

                if (! isset($setArray[$startRow]['sets'])) {
                    $setArray[$startRow]['sets'] = array();
                }
                if (! isset($setArray[$startRow]['counter'])) {
                    $setArray[$startRow]['counter'] = 0;
                }

                // Daten nur einlesen, wenn für dieses Feld ein Wert (eine Zahl 1-9) gesetzt wurde
                if ($_SESSION['pSudokuHelper']['sudoku'][$row][$col]['set'] != 0) {
                    $setArray[$startRow]['sets'][] = (int) $_SESSION['pSudokuHelper']['sudoku'][$row][$col]['set'];
                    $setArray[$startRow]['counter'] ++;
                }
            }

            // nur weiter, wenn die Regel auch angewendet werden kann
            // --> zwei 3er-Blöcke müssen jeweils 3 Zahlen aufweisen (also vollständig ausgefüllt sein)
            // --> im dritten 3er-Block darf höchstens eine Zahl stehen (also höchstens ein Feld belegt sein)
            if (($setArray[1]['counter'] === 3 && $setArray[4]['counter'] === 3 && $setArray[7]['counter'] <= 1)) {
                $startRow = 7;
            } elseif ($setArray[1]['counter'] === 3 && $setArray[7]['counter'] === 3 && $setArray[4]['counter'] <= 1) {
                $startRow = 4;
            } elseif ($setArray[4]['counter'] === 3 && $setArray[7]['counter'] === 3 && $setArray[1]['counter'] <= 1) {
                $startRow = 1;
            } else {
                // zur nächsten Zeile
                continue;
            }

            $workArray = array_diff(array(
                1,
                2,
                3,
                4,
                5,
                6,
                7,
                8,
                9
            ), array_merge($setArray[1]['sets'], $setArray[4]['sets'], $setArray[7]['sets']));

            // in $workArray sind jetzt die Zahlen, die auf die noch freien Stellen müssen
            // sie können aber nur in die freien Stellen gesetzt werden und an keiner anderen Stelle des 9er-Blocks
            // deshalb im 9er-Block alle Möglichkeiten in den Possible-Daten löschen

            $startCol = getStartRowOrCol($row);

            for ($tcol = novum($startCol); $tcol < novum($startCol) + 3; $tcol ++) {
                if ($tcol != $col) {
                    for ($trow = novum($startRow); $trow < novum($startRow) + 3; $trow ++) {
                        foreach ($workArray as $numberSet) {
                            setPossible($trow, $tcol, $numberSet, false);
                        }
                    }
                }
            }
        }

        updateStepback();

        break;

    case 'clean_up':
        
        /*
         * Regel:
         *
         * Pärchen bereinigen
         *
         * Gegeben eine Zeile, eine Spalte oder ein Unterquadrat
         * In zwei Kästchen sind gleiche Zahlen gesetzt als possible: z.B. 25 und 25 oder 245, 245 und 245
         * In der restlichen Zeile (Spalte, Unterquadrat) dürfen diese Zahlen nicht mehr unter possible auftauchen
         */
        
        updatePrevious();

        $updateRequired = false;

        // waagrechte Prüfung
        // alle Zeilen durchlaufen
        for ($row = 1; $row < 10; $row ++) {
            $arbeitsarray = array();
            $found = array();

            // alle Spalten durchlaufen
            for ($col = 1; $col < 10; $col ++) {
                $arbeitsarray[$col] = $_SESSION['pSudokuHelper']['sudoku'][$row][$col]['possible'];
            }
            $found = search_same($arbeitsarray);

            if (sizeof($found) > 0) {
                $updateRequired = true;

                for ($col = 1; $col < 10; $col ++) {

                    foreach ($found as $key => $data) {
                        if (! $data['foundCol'][$col]) {
                            // in dieser Spalte wurde nichts gefunden, deshalb die possible-Daten bearbeiten
                            foreach ($data['possibleArr'] as $posKey => $posData) {
                                if ($posData) {
                                    setPossible($row, $col, $posKey, false);
                                }
                            }
                        }
                    }
                }
            }
        }

        // senkrechte Prüfung
        // alle Spalten durchlaufen
        for ($col = 1; $col < 10; $col ++) {
            $arbeitsarray = array();
            $found = array();

            // alle Zeilen durchlaufen
            for ($row = 1; $row < 10; $row ++) {
                $arbeitsarray[$row] = $_SESSION['pSudokuHelper']['sudoku'][$row][$col]['possible'];
            }
            $found = search_same($arbeitsarray);

            if (sizeof($found) > 0) {
                $updateRequired = true;

                for ($row = 1; $row < 10; $row ++) {

                    foreach ($found as $key => $data) {
                        if (! $data['foundCol'][$row]) {
                            // in dieser Zeile wurde nichts gefunden, deshalb die possible-Daten bearbeiten
                            foreach ($data['possibleArr'] as $posKey => $posData) {
                                if ($posData) {
                                    setPossible($row, $col, $posKey, false);
                                }
                            }
                        }
                    }
                }
            }
        }

        // Unterquadratprüfung (9er-Block)
        // alle Neunerblöcke durchlaufen
        $neunerBlock = generate_neunerblock();

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
            $found = search_same($arbeitsarray);

            if (sizeof($found) > 0) {
                $updateRequired = true;
                $i = 1;
                for ($row = $blockData['row']; $row < $blockData['row'] + 3; $row ++) {
                    for ($col = $blockData['col']; $col < $blockData['col'] + 3; $col ++) {

                        foreach ($found as $key => $data) {
                            if (! $data['foundCol'][$i]) {
                                // in dieser Zeile wurde nichts gefunden, deshalb die possible-Daten bearbeiten
                                foreach ($data['possibleArr'] as $posKey => $posData) {
                                    if ($posData) {
                                        setPossible($row, $col, $posKey, false);
                                    }
                                }
                            }
                        }
                        $i ++;
                    }
                }
            }
        }

        if ($updateRequired) {
            updateStepback();
        }
        break;

    case 'save_single':

        updatePrevious();

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
                    setNumber($row, $col, $numberSet);
                }
            }
        }

        updateStepback();
        break;
}

admRedirect(ADMIDIO_URL . FOLDER_PLUGINS . PLUGIN_FOLDER . '/system/sudokuhelper.php');
