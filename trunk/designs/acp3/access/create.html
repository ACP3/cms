{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<div id="tabs" style="width:85%">
		<ul>
			<li><a href="#tab-1">{lang t="common|general_statements"}</a></li>
			<li><a href="#tab-2">{lang t="access|permissions"}</a></li>
		</ul>
		<div id="tab-1">
			<dl>
				<dt><label for="name">{lang t="common|name"}</label></dt>
				<dd><input type="text" name="form[name]" id="name" value="{$form.name}" maxlength="120"></dd>
				<dt><label for="parent">{lang t="access|superior_role"}</label></dt>
				<dd>
					<select name="form[parent]" id="parent">
{foreach $parent as $row}
						<option value="{$row.id}"{$row.selected}>{$row.name}</option>
{/foreach}
					</select>
				</dd>
			</dl>
		</div>
		<div id="tab-2">
			<table class="acp-table">
				<thead>
					<tr>
						<th>{lang t="access|module_name"}</th>
{foreach $privileges as $row}
						<th>{$row.key}</th>
{/foreach}
					</tr>
				</thead>
				<tbody>
{foreach $modules as $module => $values}
					<tr>
						<td><strong>{$module}</strong></td>
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
		<input type="reset" value="{lang t="common|reset"}" class="form">
	</div>
</form>