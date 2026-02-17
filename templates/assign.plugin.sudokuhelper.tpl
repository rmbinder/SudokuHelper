<table id="sudokuhelper_assignment_table" class="table table-hover" style="max-width: 100%;">
    <thead>
        <tr>
            {foreach $headers as $key => $header}
                <th style="text-align:{$columnAlign[$key]};{if $columnWidth[$key] !== ''} width:{$columnWidth[$key]};{/if}">{$header}</th>
            {/foreach}
        </tr>
    </thead>
   
    <tbody >
        {foreach $contents as $key => $row}
            <tr id="{$key}">
                {foreach $row as $key => $cell}
                    <td style="text-align:{$columnAlign[$key]};{if $columnWidth[$key] !== ''} width:{$columnWidth[$key]};{/if}">{$cell}</td>
                {/foreach}
            </tr>
        {/foreach}
    </tbody>
</table>