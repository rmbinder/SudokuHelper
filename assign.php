<?php
/**
 ***********************************************************************************************
 * Erzeugt die Modal-Auswahlliste für das Plugin sudokuhelper
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
 *
 *****************************************************************************/

require_once(__DIR__ . '/../../adm_program/system/common.php');
require_once(__DIR__ . '/common_function.php');

// Initialize and check the parameters
$getRow = admFuncVariableIsValid($_GET, 'row', 'int');
$getCol = admFuncVariableIsValid($_GET, 'col', 'int');

$html = '';

// set headline of the script
$headline = $gL10n->get('PLG_SUDOKU_HELPER_NAME');

$gNavigation->addUrl(CURRENT_URL, $headline);

$page = null;

header('Content-type: text/html; charset=utf-8');

$html .= '<script type="text/javascript">
    $(function() {
        $(".admidio-group-heading").click(function() {
            showHideBlock($(this).attr("id"));
        });

        $("#sudoku_assignment_form").submit(function(event) {
            var action = $(this).attr("action");
            var sudokuFormAlert = $("#sudoku_assignment_form .form-alert");
            sudokuFormAlert.hide();

            // disable default form submit
            event.preventDefault();

            $.post({
                url: action,
                data: $(this).serialize(),
                success: function(data) {
                    if (data === "success") {
                        setTimeout(function() {
                            $("#admidio_modal").modal("hide");
                        }, 100);
                       
                    } else {
                        sudokuFormAlert.attr("class", "alert alert-danger form-alert");
                        sudokuFormAlert.fadeIn();
                        sudokuFormAlert.html("<span class=\"glyphicon glyphicon-exclamation-sign\"></span>" + data);
                    }
                }
            });
        });
    });
</script>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">'.$headline.'</h4>
</div>
<div class="modal-body">';

// action for the form
$html .= '<form id="sudoku_assignment_form" action="'.safeUrl(ADMIDIO_URL . FOLDER_PLUGINS . '/sudokuhelper/assign_save.php', array('row' => $getRow, 'col' => $getCol)).'" method="post">';

// Create table
$table = new HtmlTable('sudoku_assignment_table');
$table->setColumnAlignByArray(array('center',  'center', 'center'));
$columnHeading = array(
	$gL10n->get('PLG_SUDOKU_HELPER_SET'),
    '&nbsp;',
	$gL10n->get('PLG_SUDOKU_HELPER_POSSIBLE')
);
$table->addRowHeadingByArray($columnHeading);

$table->setColumnsWidth(array('25%', '50%', '25%'));

$columnValues   = array();
$numberChecked  = '';
$possibleChecked = '';

$table->addRowByArray($columnValues);

for ($i = 1; $i < 10; $i++)
{
    if ($_SESSION['pSudokuHelper'][$getRow][$getCol]['set'] == $i)
    {
        $numberChecked = ' checked="checked" ';
    }
    else
    {
        $numberChecked  = '';
    }
    
    if ($_SESSION['pSudokuHelper'][$getRow][$getCol]['possible'][$i] )
    {
        $possibleChecked = ' checked="checked" ';
        $numberDisabled = '';
    }
    else
    {
        $possibleChecked  = '';
        $numberDisabled = ' disabled="disabled" ';
    }
    
    $columnValues = array(
    	'<input type="radio" name="set" '.$numberChecked.$numberDisabled.' value="'.$i.'" />',
        $i,
        '<input type="checkbox" id="possible-'.$i.'" name="possible-'.$i.'" '.$possibleChecked.'  value="1" />'
    );
    
    $table->addRowByArray($columnValues);
}

$html .= $table->show();

$html .= '
    <button class="btn-primary btn" id="btn_save" type="submit"><img src="'.THEME_URL.'/icons/disk.png" alt="'.$gL10n->get('SYS_SAVE').'" />&nbsp;'.$gL10n->get('SYS_SAVE').'</button>
    <div class="form-alert" style="display: none;">&nbsp;</div>
</form>';

echo $html.'</div>';			// end-div class="modal-body"

