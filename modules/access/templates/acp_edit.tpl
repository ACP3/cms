{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#tab-1" data-toggle="tab">{lang t="common|general_statements"}</a></li>
			<li><a href="#tab-2" data-toggle="tab">{lang t="access|permissions"}</a></li>
		</ul>
		<div class="tab-content">
			<div id="tab-1" class="tab-pane active">
				<div class="control-group">
					<label for="name" class="control-label">{lang t="common|name"}</label>
					<div class="controls"><input type="text" name="name" id="name" value="{$form.name}" maxlength="120"></div>
				</div>
{if isset($parent)}
				<div class="control-group">
					<label for="parent" class="control-label">{lang t="access|superior_role"}</label>
					<div class="controls">
						<select name="parent" id="parent">
{foreach $parent as $row}
							<option value="{$row.id}"{$row.selected}>{$row.name}</option>
{/foreach}
						</select>
					</div>
{/if}
				</div>
			</div>
			<div id="tab-2" class="tab-pane">
{foreach $modules as $module => $values}
				<table class="table table-striped">
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
								<label for="privileges-{$values.id}-{$privilege.id}-{$row.value}" class="radio">
									<input type="radio" name="privileges[{$values.id}][{$privilege.id}]" id="privileges-{$values.id}-{$privilege.id}-{$row.value}" value="{$row.value}"{$row.selected}>
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
	</div>
	<div class="form-actions">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="btn">
		{$form_token}
	</div>
</form>