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
				<div class="control-group">
					<label for="parent" class="control-label">{lang t="access|superior_role"}</label>
					<div class="controls">
						<select name="parent" id="parent">
{foreach $parent as $row}
							<option value="{$row.id}"{$row.selected}>{$row.name}</option>
{/foreach}
						</select>
					</div>
				</div>
			</div>
			<div id="tab-2" class="tab-pane">
{foreach $modules as $module => $values}
				<fieldset class="f-left" style="width:50%">
					<legend>{$module}</legend>
{foreach $values.privileges as $privilege}
					<div class="control-group">
						<label class="control-label"{if !empty($privilege.description)} title="{$privilege.description}"{/if}>{$privilege.key}</label>
						<div class="controls">
							<div class="btn-group" data-toggle="radio">
{foreach $privilege.select as $row}
								<input type="radio" name="privileges[{$values.id}][{$privilege.id}]" id="privileges-{$values.id}-{$privilege.id}-{$row.value}" value="{$row.value}"{$row.selected}>
								<label for="privileges-{$values.id}-{$privilege.id}-{$row.value}" class="btn">{$row.lang}</label>
{/foreach}
							</div>
						</div>
					</div>
{/foreach}
				</fieldset>
{/foreach}
			</div>
		</div>
	</div>
	<div class="form-actions">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="btn">
		<input type="reset" value="{lang t="common|reset"}" class="btn">
		{$form_token}
	</div>
</form>