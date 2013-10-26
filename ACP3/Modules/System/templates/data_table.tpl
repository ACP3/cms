{if $dt.initialized === false}
{include_js module="system" file="datatable"}
{/if}
<script type="text/javascript">
$(document).ready(function() {
	$('{$dt.element}').dataTable({
		"aLengthMenu": [[10, 15, 20, 25, 50, -1], [10, 15, 20, 25, 50, "{lang t="system|data_table_all"}"]],
		"iDisplayLength": {$dt.records_per_page},
		'bStateSave': true,
{if isset($dt.sort_col, $dt.sort_dir)}
		'aaSorting': [[ {$dt.sort_col}, "{$dt.sort_dir}" ]],
{/if}
		"oLanguage": {
			"sLoadingRecords": "{lang t="system|data_table_loading_records"}",
			"sEmptyTable": "{lang t="system|no_entries"}",
			"sSearch": "{lang t="system|data_table_search"}",
			"sLengthMenu": "{lang t="system|data_table_length_menu"}",
			"sZeroRecords": "{lang t="system|data_table_zero_records"}",
			"sInfo": "{lang t="system|data_table_info"}",
			"sInfoEmpty": "{lang t="system|data_table_info_empty"}",
			"sInfoFiltered": "{lang t="system|data_table_info_filtered"}",
			"oPaginate": {
				"sPrevious": "{lang t="system|previous"}",
				"sNext": "{lang t="system|next"}"
			}
		},
{if isset($dt.hide_col_sort) && $dt.hide_col_sort !== ''}
		"aoColumnDefs": [{
			"bSortable": false, "aTargets": [ {$dt.hide_col_sort} ]
		}]
{/if}
	});
});
</script>