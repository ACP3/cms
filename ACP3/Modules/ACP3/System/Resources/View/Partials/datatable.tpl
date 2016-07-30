{javascripts}
    {include_js module="system" file="libs/dataTables.bootstrap" depends="datatables"}
    <script type="text/javascript">
        $(document).ready(function () {
            $('{$dt.element}').dataTable({
                lengthMenu: [
                    [10, 15, 20, 25, 50, -1],
                    [10, 15, 20, 25, 50, "{lang t="system|data_table_all"}"]
                ],
                iDisplayLength: {$dt.records_per_page},
                stateSave: true,
                {if isset($dt.sort_col, $dt.sort_dir)}
                sorting: [
                    [ {$dt.sort_col}, "{$dt.sort_dir}" ]
                ],
                {/if}
                language: {
                    loadingRecords: "{lang t="system|data_table_loading_records"}",
                    emptyTable: "{lang t="system|no_entries"}",
                    search: "{lang t="system|data_table_search"}",
                    lengthMenu: "{lang t="system|data_table_length_menu"}",
                    zeroRecords: "{lang t="system|data_table_zero_records"}",
                    info: "{lang t="system|data_table_info"}",
                    infoEmpty: "{lang t="system|data_table_info_empty"}",
                    infoFiltered: "{lang t="system|data_table_info_filtered"}",
                    paginate: {
                        previous: "{lang t="system|previous"}",
                        next: "{lang t="system|next"}"
                    }
                },
                {if isset($dt.hide_col_sort) && $dt.hide_col_sort !== ''}
                columnDefs: [
                    {
                        sortable: false,
                        targets: [ {$dt.hide_col_sort} ]
                    }
                ]
                {/if}
            });
        });
    </script>
{/javascripts}
