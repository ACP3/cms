{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{uri args="acp/contact/list"}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#tab-1" data-toggle="tab">{lang t="contact|imprint"}</a></li>
			<li><a href="#tab-2" data-toggle="tab">{lang t="contact|layout"}</a></li>
		</ul>
		<div class="tab-content">
			<div id="tab-1" class="tab-pane active">
				<div class="control-group">
					<label for="address" class="control-label">{lang t="contact|address"}</label>
					<div class="controls">{wysiwyg name="address" value="`$form.address`" height="150" toolbar="simple"}</div>
				</div>
				<div class="control-group">
					<label for="mail" class="control-label">{lang t="common|email"}</label>
					<div class="controls"><input type="email" name="mail" id="mail" value="{$form.mail}" maxlength="120"></div>
				</div>
				<div class="control-group">
					<label for="telephone" class="control-label">{lang t="contact|telephone"}</label>
					<div class="controls"><input type="tel" name="telephone" id="telephone" value="{$form.telephone}" maxlength="120"></div>
				</div>
				<div class="control-group">
					<label for="fax" class="control-label">{lang t="contact|fax"}</label>
					<div class="controls"><input type="tel" name="fax" id="fax" value="{$form.fax}" maxlength="120"></div>
				</div>
				<div class="control-group">
					<label for="disclaimer" class="control-label">{lang t="contact|disclaimer"}</label>
					<div class="controls">{wysiwyg name="disclaimer" value="`$form.disclaimer`" height="150px" toolbar="simple"}</div>
				</div>
			</div>
			<div id="tab-2" class="tab-pane">
				<div class="control-group">
					<label for="layout" class="control-label">{lang t="contact|layout"}</label>
					<div class="controls">
						{wysiwyg name="layout" value="`$form.layout`" height="200px" toolbar="simple"}
						<p class="help-block">
							<strong>{lang t="contact|layout_placeholders"}</strong>
						</p>
{literal}
							<ul>
								<li>{address_lang}, {address_value}</li>
								<li>{email_lang}, {email_value}</li>
								<li>{telephone_lang}, {telephone_value}</li>
								<li>{fax_lang}, {fax_value}</li>
								<li>{disclaimer_lang}, {disclaimer_value}</li>
							</ul>
{/literal}
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="form-actions">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="btn">
		{$form_token}
	</div>
</form>