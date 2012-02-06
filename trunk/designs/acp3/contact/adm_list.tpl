{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{uri args="acp/contact/adm_list"}" method="post" accept-charset="UTF-8">
	<div id="tabs">
		<ul>
			<li><a href="#tab-1">{lang t="contact|imprint"}</a></li>
			<li><a href="#tab-2">{lang t="contact|layout"}</a></li>
		</ul>
		<div id="tab-1">
			<dl>
				<dt><label for="address">{lang t="contact|address"}</label></dt>
				<dd>{wysiwyg name="address" value="`$form.address`" height="150" toolbar="simple"}</dd>
				<dt><label for="mail">{lang t="common|email"}</label></dt>
				<dd><input type="email" name="form[mail]" id="mail" value="{$form.mail}" maxlength="120"></dd>
				<dt><label for="telephone">{lang t="contact|telephone"}</label></dt>
				<dd><input type="tel" name="form[telephone]" id="telephone" value="{$form.telephone}" maxlength="120"></dd>
				<dt><label for="fax">{lang t="contact|fax"}</label></dt>
				<dd><input type="tel" name="form[fax]" id="fax" value="{$form.fax}" maxlength="120"></dd>
				<dt><label for="disclaimer">{lang t="contact|disclaimer"}</label></dt>
				<dd>{wysiwyg name="disclaimer" value="`$form.disclaimer`" height="150px" toolbar="simple"}</dd>
			</dl>
		</div>
		<div id="tab-2" class="ui-tabs-hide">
			<dl>
				<dt><label for="layout">{lang t="contact|layout"}</label></dt>
				<dd>
					{wysiwyg name="layout" value="`$form.layout`" height="200px" toolbar="simple"}
					<p>
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
				</dd>
			</dl>
		</div>
	</div>
	<div class="form-bottom">
		<input type="submit" value="{lang t="common|submit"}" class="form">
		{$form_token}
	</div>
</form>