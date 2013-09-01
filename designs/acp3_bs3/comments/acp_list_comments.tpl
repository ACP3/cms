{if isset($comments)}
	<form action="{uri args="acp/comments/delete_comments"}" method="post">
		<nav id="adm-list" class="navbar navbar-default" role="navigation">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex2-collapse">
					<span class="sr-only">{lang t="system|toggle_navigation"}</span>
					<span class="glyphicon glyphicon-bar"></span>
					<span class="glyphicon glyphicon-bar"></span>
					<span class="glyphicon glyphicon-bar"></span>
				</button>
				<span class="navbar-brand">{lang t="system|overview"}</span>
			</div>
			<div class="collapse navbar-collapse navbar-ex2-collapse">
				<div class="navbar-text pull-right">
					{check_access mode="input" path="acp/comments/delete_comments" icon="32/cancel" lang="system|delete_marked"}
				</div>
			</div>
		</nav>
		{if isset($redirect_message)}
			{$redirect_message}
		{/if}
		<table id="acp-table" class="table table-striped table-hover">
			<thead>
				<tr>
					{if $can_delete === true}
						<th style="width:3%"><input type="checkbox" id="mark-all" value="1"></th>
						{/if}
					<th style="width:22%">{lang t="system|date"}</th>
					<th>{lang t="system|name"}</th>
					<th>{lang t="system|message"}</th>
					<th>{lang t="comments|ip"}</th>
					<th style="width:5%">{lang t="system|id"}</th>
				</tr>
			</thead>
			<tbody>
				{foreach $comments as $row}
					<tr>
						{if $can_delete === true}
							<td><input type="checkbox" name="entries[]" value="{$row.id}"></td>
							{/if}
						<td>{$row.date_formatted}</td>
						<td>{check_access mode="link" path="acp/comments/edit/id_`$row.id`" title=$row.name}</td>
						<td>{$row.message}</td>
						<td>{$row.ip}</td>
						<td>{$row.id}</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
		{if $can_delete === true}
			{mark name="entries"}
		{/if}
	</form>
{else}
	<div class="alert alert-warning text-center">
		<strong>{lang t="system|no_entries"}</strong>
	</div>
{/if}