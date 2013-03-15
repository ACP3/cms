{if isset($redirect_message)}
{$redirect_message}
{/if}
<div class="tabbable">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|installed_modules"}</a></li>
		<li><a href="#tab-2" data-toggle="tab">{lang t="system|installable_modules"}</a></li>
	</ul>
	<div class="tab-content">
		<div id="tab-1" class="tab-pane active">
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th>{lang t="system|module_name"}</th>
						<th>{lang t="system|description"}</th>
						<th>{lang t="system|version"}</th>
						<th>{lang t="system|author"}</th>
						<th>{lang t="system|options"}</th>
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
							{icon path="16/editdelete" width="16" height="16" alt={lang t="system|protected_module"} title={lang t="system|protected_module_description"}}
{else}
							<div class="btn-group">
{if $row.active === true}
								<a href="{uri args="acp/system/modules/dir_`$row.dir`/action_deactivate"}" class="btn btn-small" title="{lang t="system|disable_module"}"><i class="icon-remove"></i> {lang t="system|disable"}</a>
{else}
								<a href="{uri args="acp/system/modules/dir_`$row.dir`/action_activate"}" class="btn btn-small" title="{lang t="system|enable_module"}"><i class="icon-ok"></i> {lang t="system|enable"}</a>
{/if}
								<a href="{uri args="acp/system/modules/dir_`$row.dir`/action_uninstall"}" class="btn btn-small" title="{lang t="system|uninstall_module"}"><i class="icon-off"></i> {lang t="system|uninstall"}</a>
							</div>
{/if}
						</td>
					</tr>
{/foreach}
				</tbody>
			</table>
		</div>
		<div id="tab-2" class="tab-pane">
{if !empty($new_modules)}
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th>{lang t="system|module_name"}</th>
						<th>{lang t="system|description"}</th>
						<th>{lang t="system|version"}</th>
						<th>{lang t="system|author"}</th>
						<th>{lang t="system|options"}</th>
					</tr>
				</thead>
				<tbody>
{foreach $new_modules as $row}
					<tr>
						<td>{$row.name}</td>
						<td>{$row.description}</td>
						<td>{$row.version}</td>
						<td>{$row.author}</td>
						<td><a href="{uri args="acp/system/modules/dir_`$row.dir`/action_install"}" class="btn btn-small" title="{lang t="system|install_module"}"><i class="icon-off"></i> {lang t="system|install"}</a></td>
					</tr>
{/foreach}
				</tbody>
			</table>
{else}
			<div class="alert align-center">
				<strong>{lang t="system|no_modules_available_for_installation"}</strong>
			</div>
{/if}
		</div>
	</div>
</div>