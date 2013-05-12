{if isset($error_msg)}
{$error_msg}
{/if}
{include_js module="menus" file="acp"}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|general_statements"}</a></li>
			<li><a href="#tab-2" data-toggle="tab">{lang t="menus|page_type"}</a></li>
			<li><a href="#tab-3" data-toggle="tab">{lang t="system|seo"}</a></li>
		</ul>
		<div class="tab-content">
			<div id="tab-1" class="tab-pane active">
				<div class="control-group">
					<label for="mode" class="control-label">{lang t="menus|page_type"}</label>
					<div class="controls">
						<select name="mode" id="mode">
{foreach $mode as $row}
							<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
						</select>
					</div>
				</div>
				<div class="control-group">
					<label for="title" class="control-label">{lang t="menus|title"}</label>
					<div class="controls"><input type="text" name="title" id="title" value="{$form.title}" maxlength="120" required></div>
				</div>
				<div class="control-group">
					<label for="block-id" class="control-label">{lang t="menus|menu_bar"}</label>
					<div class="controls">
						<select name="block_id" id="block-id">
{foreach $blocks as $row}
							<option value="{$row.id}"{$row.selected}>{$row.title}</option>
{/foreach}
						</select>
					</div>
				</div>
				<div class="control-group">
					<label for="parent" class="control-label">{lang t="menus|superior_page"}</label>
					<div class="controls">
						<select name="parent" id="parent">
							<option value="">{lang t="menus|no_superior_page"}</option>
{foreach $pages_list as $blocks}
							<optgroup label="{$blocks.title}">
{foreach $blocks.items as $row}
								<option value="{$row.id}"{$row.selected}>{$row.spaces}{$row.title}</option>
{/foreach}
							</optgroup>
{/foreach}
						</select>
					</div>
				</div>
				<div class="control-group">
					<label for="{$display.0.id}" class="control-label">{lang t="menus|display_item"}</label>
					<div class="controls">
						<div class="btn-group" data-toggle="radio">
{foreach $display as $row}
							<input type="radio" name="display" id="{$row.id}" value="{$row.value}"{$row.checked}>
							<label for="{$row.id}" class="btn">{$row.lang}</label>
{/foreach}
						</div>
					</div>
				</div>
			</div>
			<div id="tab-2" class="tab-pane">
				<div id="module-container" class="control-group">
					<label for="module" class="control-label">{lang t="menus|module"}</label>
					<div class="controls">
						<select name="module" id="module">
							<option value="">{lang t="system|pls_select"}</option>
{foreach $modules as $row}
							<option value="{$row.dir}"{$row.selected}>{$row.name}</option>
{/foreach}
						</select>
					</div>
				</div>
				<div id="link-container" class="control-group">
					<label for="uri" class="control-label">{lang t="menus|uri"}</label>
					<div class="controls">
						<input type="text" name="uri" id="uri" value="{$form.uri}" maxlength="120">
						<p id="link-hints" class="help-block">{lang t="menus|dynamic_page_hints"}</p>
					</div>
				</div>
{if isset($articles)}
				<div id="articles-container" class="control-group">
					<label for="articles" class="control-label">{lang t="articles|articles"}</label>
					<div class="controls">
						<select name="articles" id="articles">
							<option value="">{lang t="system|pls_select"}</option>
{foreach $articles as $row}
							<option value="{$row.id}"{$row.selected}>{$row.title}</option>
{/foreach}
						</select>
					</div>
				</div>
{/if}
				<div id="target-container" class="control-group">
					<label for="target" class="control-label">{lang t="menus|target_page"}</label>
					<div class="controls">
						<select name="target" id="target">
{foreach $target as $row}
							<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
						</select>
					</div>
				</div>
			</div>
			<div id="tab-3" class="tab-pane">
				{$SEO_FORM_FIELDS}
			</div>
		</div>
	</div>
	<div class="form-actions">
		<button type="submit" name="submit" class="btn">{lang t="system|submit"}</button>
		<a href="{uri args="acp/menus"}" class="btn">{lang t="system|cancel"}</a>
		{$form_token}
	</div>
</form>