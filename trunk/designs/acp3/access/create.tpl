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
				<dd><input type="text" name="name" id="name" value="{$form.name}" maxlength="120"></dd>
				<dt><label for="parent">{lang t="access|superior_role"}</label></dt>
				<dd>
					<select name="parent" id="parent">
{foreach $parent as $row}
						<option value="{$row.id}"{$row.selected}>{$row.name}</option>
{/foreach}
					</select>
				</dd>
			</dl>
		</div>
		<div id="tab-2">
{foreach $modules as $module => $values}
			<table class="privileges">
				<thead>
					<tr>
						<th colspan="4">{$module}</th>
					</tr>
				</thead>
				<tbody>
{foreach $values.privileges as $privilege}
					<tr>
						<td class="privilege-name"{if !empty($privilege.description)} title="{$privilege.description}"{/if}>{$privilege.key}</td>
{foreach $privilege.select as $row}
						<td>
							<label for="privileges-{$values.id}-{$privilege.id}-{$row.value}">
								<input type="radio" name="privileges[{$values.id}][{$privilege.id}]" id="privileges-{$values.id}-{$privilege.id}-{$row.value}" value="{$row.value}" class="checkbox"{$row.selected}>
								{$row.lang}
							</label>
						</td>
{/foreach}
					</tr>
{/foreach}
				</tbody>
			</table>
{/foreach}
		</div>
	</div>
	<div class="form-bottom">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="form">
		<input type="reset" value="{lang t="common|reset"}" class="form">
		{$form_token}
	</div>
</form>