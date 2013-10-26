<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">{lang t="users|user_menu"}</h3>
	</div>
	<div class="panel-body">
		<div class="list-group" style="margin-bottom: 0">
			<a href="{uri args="users/home"}" class="list-group-item"><i class="glyphicon glyphicon-home"></i> {lang t="users|home"}</a>
			{if isset($user_sidebar.modules)}
				<div class="dropdown">
					<a href="{uri args="acp"}" class="list-group-item dropdown-toggle" data-toggle="dropdown" data-target="#">
						<i class="glyphicon glyphicon-file"></i>
						{lang t="users|administration"}
						<b class="caret"></b>
					</a>
					<ul class="dropdown-menu">
						{foreach $user_sidebar.modules as $row}
							<li{$row.active}><a href="{uri args="acp/`$row.dir`"}">{$row.name}</a></li>
							{/foreach}
					</ul>
				</div>
			{/if}
			{if isset($user_sidebar.system)}
				<div class="dropdown">
					<a href="{uri args="acp/system"}" class="list-group-item dropdown-toggle" data-toggle="dropdown" data-target="#">
						<i class="glyphicon glyphicon-wrench"></i>
						{lang t="system|system"}
						<b class="caret"></b>
					</a>
					<ul class="dropdown-menu">
						{foreach $user_sidebar.system as $row}
							<li{$row.active}><a href="{uri args="acp/system/`$row.page`"}">{$row.name}</a></li>
							{/foreach}
					</ul>
				</div>
			{/if}
			<a href="{uri args="users/logout/last_`$user_sidebar.page`"}" class="list-group-item"><i class="glyphicon glyphicon-off"></i> {lang t="users|logout"}</a>
		</div>
	</div>
</div>