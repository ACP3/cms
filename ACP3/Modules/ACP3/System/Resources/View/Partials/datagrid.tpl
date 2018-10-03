{if $dataTable.num_results > 0}
    <table id="{$dataTable.identifier}"
           class="table table-striped table-hover datagrid"
           data-datatable-init="{$dataTable.config.config|escape}">
        <thead>
        <tr>
            {$dataTable.header}
        </tr>
        </thead>
        {if !empty($dataTable.results)}
            <tbody>
            {$dataTable.results}
            </tbody>
        {/if}
    </table>
    {if $dataTable.can_delete === true}
        {include file="asset:System/Partials/mark.tpl"}
    {/if}
    {javascripts}
    {include_js module="system" file="dataTables.bootstrap" depends="datatables"}
    {include_js module="system" file="partials/datagrid" depends="datatables"}
    {/javascripts}
{else}
    {include file="asset:System/Partials/no_results.tpl"}
{/if}
