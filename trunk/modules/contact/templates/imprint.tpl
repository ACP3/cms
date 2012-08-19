<dl>
{if !empty($imprint.address)}
	<dt>{lang t="contact|address"}</dt>
	<dd>{$imprint.address}</dd>
{/if}
{if !empty($imprint.mail)}
	<dt>{lang t="common|email"}</dt>
	<dd>{$imprint.mail}</dd>
{/if}
{if !empty($imprint.telephone)}
	<dt>{lang t="contact|telephone"}</dt>
	<dd>{$imprint.telephone}</dd>
{/if}
{if !empty($imprint.fax)}
	<dt>{lang t="contact|fax"}</dt>
	<dd>{$imprint.fax}</dd>
{/if}
{if !empty($imprint.disclaimer)}
	<dt>{lang t="contact|disclaimer"}</dt>
	<dd>{$imprint.disclaimer}</dd>
{/if}
</dl>
<p class="powered-by align-center">
	{$powered_by}
</p>