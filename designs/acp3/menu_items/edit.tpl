{if isset($error_msg)}
{$error_msg}
{/if}
<script type="text/javascript" src="{$DESIGN_PATH}menu_items/script.js"></script>
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<div id="tabs">
		<ul>
			<li><a href="#tab-1">{lang t="common|general_statements"}</a></li>
			<li><a href="#tab-2">{lang t="menu_items|page_type"}</a></li>
			<li><a href="#tab-3">{lang t="common|seo"}</a></li>
		</ul>
		<div id="tab-1">
			<dl>
				<dt><label for="mode">{lang t="menu_items|page_type"}</label></dt>
				<dd>
					<select name="mode" id="mode">
{foreach $mode as $row}
						<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
					</select>
				</dd>
				<dt><label for="title">{lang t="menu_items|title"}</label></dt>
				<dd><input type="text" name="title" id="title" value="{$form.title}" maxlength="120"></dd>
			</dl>
			<dl>
				<dt><label for="block-id">{lang t="menu_items|blocks"}</label></dt>
				<dd>
					<select name="block_id" id="block-id">
						<option value="">{lang t="common|pls_select"}</option>
{foreach $blocks as $row}
						<option value="{$row.id}"{$row.selected}>{$row.title}</option>
{/foreach}
					</select>
				</dd>
				<dt><label for="parent">{lang t="menu_items|superior_page"}</label></dt>
				<dd>
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
				</dd>
				<dt><label for="display-1">{lang t="menu_items|display_item"}</label></dt>
				<dd>
{foreach $display as $row}
					<label for="display-{$row.value}">
						<input type="radio" name="display" id="display-{$row.value}" value="{$row.value}" class="checkbox"{$row.selected}>
						{$row.lang}
					</label>
{/foreach}
				</dd>
			</dl>
		</div>
		<div id="tab-2">
			<dl id="module-container">
				<dt><label for="module">{lang t="menu_items|module"}</label></dt>
				<dd>
					<select name="module" id="module">
						<option value="">{lang t="common|pls_select"}</option>
{foreach $modules as $row}
						<option value="{$row.dir}"{$row.selected}>{$row.name}</option>
{/foreach}
					</select>
				</dd>
			</dl>
			<dl id="link-container">
				<dt>
					<label for="uri">{lang t="menu_items|uri"}</label>
					<span id="link-hints">({lang t="menu_items|dynamic_page_hints"})</span>
				</dt>
				<dd><input type="text" name="uri" id="uri" value="{$form.uri}" maxlength="120"></dd>
			</dl>
{if isset($static_pages)}
			<dl id="static-pages-container">
				<dt><label for="static-pages">{lang t="static_pages|static_pages"}</label></dt>
				<dd>
					<select name="static_pages" id="static-pages">
						<option value="">{lang t="common|pls_select"}</option>
{foreach $static_pages as $row}
						<option value="{$row.id}"{$row.selected}>{$row.title}</option>
{/foreach}
					</select>
				</dd>
			</dl>
{/if}
			<dl id="target-container">
				<dt><label for="target">{lang t="menu_items|target_page"}</label></dt>
				<dd>
					<select name="target" id="target">
{foreach $target as $row}
						<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
					</select>
				</dd>
			</dl>
		</div>
		<div id="tab-3">
			{$SEO_FORM_FIELDS}
		</div>
	</div>
	<div class="form-bottom">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="form">
		{$form_token}
	</div>
</form>