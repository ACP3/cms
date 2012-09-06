{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|general_statements"}</a></li>
			<li><a href="#tab-2" data-toggle="tab">{lang t="system|seo"}</a></li>
		</ul>
		<div class="tab-content">
			<div id="tab-1" class="tab-pane active">
				{$publication_period}
				<div class="control-group">
					<label for="name" class="control-label">{lang t="gallery|title"}</label>
					<div class="controls"><input type="text" name="name" id="name" value="{$form.name}" maxlength="120"></div>
				</div>
			</div>
			<div id="tab-2" class="tab-pane">
				{$SEO_FORM_FIELDS}
			</div>
		</div>
	</div>
	<div class="form-actions">
		<button type="submit" name="submit" class="btn">{lang t="system|submit"}</button>
		<a href="{uri args="acp/gallery"}" class="btn">{lang t="system|cancel"}</a>
		{$form_token}
	</div>
</form>