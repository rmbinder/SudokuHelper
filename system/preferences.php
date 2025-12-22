<?php
/**
 ***********************************************************************************************
 * SudokuHelper preferences
 *
 * @copyright The Admidio Team
 * @see https://www.admidio.org/
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2.0 only
 *
 * Parameters:
 *
 * mode     : html           - (default) Show page with all preferences panels
 *            html_form      - Returns the html of the requested form
 *            save           - Save organization preferences
 * panel    : The name of the preferences panel that should be shown or saved.
 ***********************************************************************************************
 */

use Admidio\Infrastructure\Exception;
use Plugins\SudokuHelper\classes\Presenter\SudokuHelperPreferencesPresenter;

try {
    require_once (__DIR__ . '/../../../system/common.php');
    require_once (__DIR__ . '/common_function.php');

    // only authorized user are allowed to start this module
    if (! $gCurrentUser->isAdministrator()) {
        throw new Exception('SYS_NO_RIGHTS');
    }

    // Initialize and check the parameters
    $getMode = admFuncVariableIsValid($_GET, 'mode', 'string', array(
        'defaultValue' => 'html',
        'validValues' => array(
            'html',
            'html_form'
        )
    ));
    $getPanel = admFuncVariableIsValid($_GET, 'panel', 'string');

    switch ($getMode) {
        case 'html':
            // create html page object
            $page = new SudokuHelperPreferencesPresenter($getPanel);

            $gNavigation->addUrl(CURRENT_URL, $page->getHeadline());

            $page->show();
            break;

        // Returns the html of the requested form
        case 'html_form':
            $preferencesUI = new SudokuHelperPreferencesPresenter('adm_preferences_form');
            $methodName = 'create' . str_replace('_', '', ucwords($getPanel, '_')) . 'Form';
            echo $preferencesUI->{$methodName}();
            break;
    }
} catch (Throwable $exception) {
    if ($getMode === 'html_form') {
        echo $exception->getMessage();
    } else {
        $gMessage->show($exception->getMessage());
    }
}
