{if isset($error_msg)}
{$error_msg}
{/if}
<script type="text/javascript" src="{$DESIGN_PATH}js/menu_items_admin.js"></script>
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#tab-1" data-toggle="tab">{lang t="common|general_statements"}</a></li>
			<li><a href="#tab-2" data-toggle="tab">{lang t="menu_items|page_type"}</a></li>
			<li><a href="#tab-3" data-toggle="tab">{lang t="common|seo"}</a></li>
		</ul>
		<div class="tab-content">
			<div id="tab-1" class="tab-pane active">
				<div class="control-group">
					<label for="mode" class="control-label">{lang t="menu_items|page_type"}</label>
					<div class="controls">
						<select name="mode" id="mode">
{foreach $mode as $row}
							<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
						</select>
					</div>
				</div>
				<div class="control-group">
					<label for="title" class="control-label">{lang t="menu_items|title"}</label>
					<div class="controls"><input type="text" name="title" id="title" value="{$form.title}" maxlength="120"></div>
				</div>
				<div class="control-group">
					<label for="block-id" class="control-label">{lang t="menu_items|blocks"}</label>
					<div class="controls">
						<select name="block_id" id="block-id">
							<option value="">{lang t="common|pls_select"}</option>
{foreach $blocks as $row}
							<option value="{$row.id}"{$row.selected}>{$row.title}</option>
{/foreach}
						</select>
					</div>
				</div>
				<div class="control-group">
					<label for="parent" class="control-label">{lang t="menu_items|superior_page"}</label>
					<div class="controls">
						<select name="parent" id="parent">
							<option value="">{lang t="menu_items|no_superior_page"}</option>
{foreach $pages_list as $block => $pages}
							<optgroup label="{$block}">
{foreach $pages as $row}
								<option value="{$row.id}"{$row.selected}>{$row.spaces}{$row.title}</option>
{/foreach}
							</optgroup>
{/foreach}
						</select>
					</div>
				</div>
				<div class="control-group">
					<label for="display-1" class="control-label">{lang t="menu_items|display_item"}</label>
					<div class="controls">
						<div class="btn-group" data-toggle="radio">
{foreach $display as $row}
							<input type="radio" name="display" id="display-{$row.value}" value="{$row.value}"{$row.selected}>
							<label for="display-{$row.value}" class="btn">{$row.lang}</label>
{/foreach}
						</div>
					</div>
				</div>
			</div>
			<div id="tab-2" class="tab-pane">
				<div id="module-container" class="control-group">
					<label for="module" class="control-label">{lang t="menu_items|module"}</label>
					<div class="controls">
						<select name="module" id="module">
							<option value="">{lang t="common|pls_select"}</option>
{foreach $modules as $row}
							<option value="{$row.dir}"{$row.selected}>{$row.name}</option>
{/foreach}
						</select>
					</div>
				</div>
				<div id="link-container" class="control-group">
					<label for="uri" class="control-label">{lang t="menu_items|uri"}</label>
					<div class="controls">
						<input type="text" name="uri" id="uri" value="{$form.uri}" maxlength="120">
						<p id="link-hints" class="help-block">{lang t="menu_items|dynamic_page_hints"}</p>
					</div>
				</div>
{if isset($static_pages)}
				<div id="static-pages-container" class="control-group">
					<label for="static-pages" class="control-label">{lang t="static_pages|static_pages"}</label>
					<div class="controls">
						<select name="static_pages" id="static-pages">
							<option value="">{lang t="common|pls_select"}</option>
{foreach $static_pages as $row}
							<option value="{$row.id}"{$row.selected}>{$row.title}</option>
{/foreach}
						</select>
					</div>
				</div>
{/if}
				<div id="target-container" class="control-group">
					<label for="target" class="control-label">{lang t="menu_items|target_page"}</label>
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
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="btn">
		<input type="reset" value="{lang t="common|reset"}" class="btn">
		{$form_token}
	</div>
</form>