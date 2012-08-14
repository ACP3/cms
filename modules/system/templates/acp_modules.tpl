<div class="tabbable">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|installed_modules"}</a></li>
		<li><a href="#tab-2" data-toggle="tab">{lang t="system|installable_modules"}</a></li>
	</ul>
	<div class="tab-content">
		<div id="tab-1" class="tab-pane active">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>{lang t="system|module_name"}</th>
						<th>{lang t="common|description"}</th>
						<th>{lang t="system|version"}</th>
						<th>{lang t="common|author"}</th>
						<th>{lang t="common|options"}</th>
					</tr>
				</thead>
				<tbody>
{foreach $installed_modules as $row}
					<tr>
						<td>{$row.name}</td>
						<td>{$row.description}</td>
						<td>{$row.version}</td>
						<td>{$row.author}</td>
						<td>
{if $row.protected === true}
							{icon path="16/editdelete" width="16" height="16"}
{elseif $row.active === true}
							<a href="{uri args="acp/system/modules/dir_`$row.dir`/action_deactivate"}" title="{lang t="system|disable_module"}">{icon path="16/apply" width="16" height="16"}</a>
{else}
							<a href="{uri args="acp/system/modules/dir_`$row.dir`/action_activate"}" title="{lang t="system|enable_module"}">{icon path="16/cancel" width="16" height="16"}</a>
{/if}
						</td>
					</tr>
{/foreach}
				</tbody>
			</table>
		</div>
		<div id="tab-2" class="tab-pane">
{if !empty($new_modules)}
			<table class="table table-striped">
				<thead>
					<tr>
						<th>{lang t="system|module_name"}</th>
						<th>{lang t="common|description"}</th>
						<th>{lang t="system|version"}</th>
						<th>{lang t="common|author"}</th>
						<th>{lang t="common|options"}</th>
					</tr>
				</thead>
				<tbody>
{foreach $new_modules as $row}
					<tr>
						<td>{$row.name}</td>
						<td>{$row.description}</td>
						<td>{$row.version}</td>
						<td>{$row.author}</td>
						<td><a href="{uri args="acp/system/modules/dir_`$row.dir`/action_install"}" title="{lang t="system|install_module"}">{icon path="16/edit_add" width="16" height="16"}</a></td>
					</tr>
{/foreach}
				</tbody>
			</table>
{else}
			<div class="alert alert-block align-center">
				<h5>{lang t="system|no_modules_available_for_installation"}</h5>
			</div>
{/if}
		</div>
	</div>
</div>