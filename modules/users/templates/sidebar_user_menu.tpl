<ul class="nav nav-list">
	<li class="nav-header">{lang t="users|user_menu"}</li>
	<li><a href="{uri args="users/home"}"><i class="icon-home"></i> {lang t="users|home"}</a></li>
{if isset($user_sidebar.modules)}
	<li class="dropdown">
		<a href="{uri args="acp"}" class="dropdown-toggle" data-toggle="dropdown" data-target="#"><i class="icon-file"></i> {lang t="users|administration"}</a>
		<ul class="dropdown-menu">
{foreach $user_sidebar.modules as $row}
			<li><a href="{uri args="acp/`$row.dir`"}">{$row.name}</a></li>
{/foreach}
		</ul>
	</li>
{/if}
{if isset($user_sidebar.system)}
	<li class="dropdown">
		<a href="{uri args="acp/system"}" class="dropdown-toggle" data-toggle="dropdown" data-target="#"><i class="icon-wrench"></i> {lang t="system|system"}</a>
		<ul class="dropdown-menu">
{foreach $user_sidebar.system as $row}
			<li><a href="{uri args="acp/system/`$row.page`"}">{$row.name}</a></li>
{/foreach}
		</ul>
	</li>
{/if}
	<li class="divider"></li>
	<li><a href="{uri args="users/logout/last_`$user_sidebar.page`"}"><i class="icon-share"></i> {lang t="users|logout"}</a></li>
</ul>