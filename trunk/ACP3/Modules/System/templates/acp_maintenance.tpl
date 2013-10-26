<nav id="adm-list" class="navbar navbar-default" role="navigation">
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex2-collapse">
			<span class="sr-only">{lang t="system|toggle_navigation"}</span>
			<span class="glyphicon glyphicon-bar"></span>
			<span class="glyphicon glyphicon-bar"></span>
			<span class="glyphicon glyphicon-bar"></span>
		</button>
	</div>
	<div class="collapse navbar-collapse navbar-ex2-collapse">
		<div class="navbar-text pull-right">
			{check_access mode="link" path="acp/system/update_check" icon="32/cnr-update" width="32" height="32"}
			{check_access mode="link" path="acp/system/sql_import" icon="32/db_comit" width="32" height="32"}
			{check_access mode="link" path="acp/system/sql_export" icon="32/db_update" width="32" height="32"}
		</div>
	</div>
</nav>
<div class="alert alert-warning text-center">
	<strong>{lang t="system|select_menu_item"}</strong>
</div>