<form {foreach $attributes as $attribute}
        {$attribute@key}="{$attribute}"
    {/foreach}>
 
    <h3>{$l10n->get('PLG_SUDOKU_HELPER_CONGRATULATIONS')}</h3>

    {$l10n->get('PLG_SUDOKU_HELPER_SUCCESS_MESSAGE')}
    <br><br>
    {include 'sys-template-parts/form.button.tpl' data=$elements['btn_exit']}<br>
    <br><br>
    {include 'sys-template-parts/form.button.tpl' data=$elements['btn_continue']}<br>
    <br><br>
    <div class="form-alert" style="display: none;">&nbsp;</div>
   
</form>
