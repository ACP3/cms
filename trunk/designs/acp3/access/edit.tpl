{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<div id="tabs">
		<ul>
			<li><a href="#tab-1">{lang t="common|general_statements"}</a></li>
			<li><a href="#tab-2">{lang t="access|permissions"}</a></li>
		</ul>
		<div id="tab-1">
			<dl>
				<dt><label for="name">{lang t="common|name"}</label></dt>
				<dd><input type="text" name="form[name]" id="name" value="{$form.name}" maxlength="120"></dd>
{if isset($parent)}
				<dt><label for="parent">{lang t="access|superior_role"}</label></dt>
				<dd>
					<select name="form[parent]" id="parent">
{foreach $parent as $row}
						<option value="{$row.id}"{$row.selected}>{$row.name}</option>
{/foreach}
					</select>
				</dd>
{/if}
			</dl>
		</div>
		<div id="tab-2">
			<script type="text/javascript" src="{$DESIGN_PATH}access/script.js"></script>
			<table id="resources-table" class="acp-table">
				<thead>
					<tr>
{foreach $privileges as $row}
						<th>{$row.key}</th>
{/foreach}
					</tr>
				</thead>
				<tbody>
{foreach $modules as $module => $values}
					<tr>
						<th id="{$values.id}-resources" class="sub-table-header" colspan="{count($privileges)}" style="text-align:left">{$module}</th>
					</tr>
					<tr class="hide {$values.id}-resources">
{foreach $values.privileges as $privilege}
						<td>
							<select name="form[privileges][{$values.id}][{$privilege.id}]">
{foreach $privilege.select as $row}
								<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
							</select>
						</td>
{/foreach}
					</tr>
{/foreach}
				</tbody>
			</table>
		</div>
	</div>
	<div class="form-bottom">
		<input type="submit" value="{lang t="common|submit"}" class="form">
		{$form_token}
	</div>
</form>