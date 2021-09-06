{if $dataTable.num_results > 0}
    {if isset($DELETE_ROUTE)}
        <form action="{$DELETE_ROUTE}"
              method="post"
              accept-charset="UTF-8"
              novalidate
              data-ajax-form="true"
              data-ajax-form-loading-text="{lang t="system|loading_please_wait"}"
              data-ajax-form-custom-form-data='{ "action":"confirmed","submit":true }'>
            <table id="{$dataTable.identifier}"
                   class="table table-striped table-hover datagrid align-middle"
                   data-datatable-init="{$dataTable.config.config|escape}">
                <thead>
                <tr>
                    {$dataTable.header}
                </tr>
                </thead>
                {if $dataTable.show_mass_delete === true}
                    {include file="asset:System/Partials/datagrid-mass-action-bar.tpl" dataGridIdentifier=$dataTable.identifier dataGridColumnCount=$dataTable.column_count}
                {/if}
                {if !empty($dataTable.results)}
                    <tbody>
                    {$dataTable.results}
                    </tbody>
                {/if}
            </table>
            {if $dataTable.can_delete === true}
                {include file="asset:System/Partials/mark.tpl" dataGridIdentifier=$dataTable.identifier}
            {/if}
        </form>
    {else}
        <table id="{$dataTable.identifier}"
               class="table table-striped table-hover datagrid align-middle"
               data-datatable-init="{$dataTable.config.config|escape}">
            <thead>
            <tr>
                {$dataTable.header}
            </tr>
            </thead>
            {if $dataTable.show_mass_delete === true}
                {include file="asset:System/Partials/datagrid-mass-action-bar.tpl" dataGridIdentifier=$dataTable.identifier dataGridColumnCount=$dataTable.column_count}
            {/if}
            {if !empty($dataTable.results)}
                <tbody>
                {$dataTable.results}
                </tbody>
            {/if}
        </table>
    {/if}
    {javascripts}
        {js_libraries enable="ajax-form"}
        {include_js module="system" file="partials/datagrid" depends="datatables"}
    {/javascripts}
{else}
    {include file="asset:System/Partials/no_results.tpl"}
{/if}
