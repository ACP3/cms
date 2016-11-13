{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <dl>
        {if !empty($imprint.address)}
            <dt>{lang t="contact|address"}</dt>
            <dd>{$imprint.address|rewrite_uri}</dd>
        {/if}
        {if !empty($imprint.mail)}
            <dt>{lang t="system|email_address"}</dt>
            <dd><a href="{uri args="contact"}">{$imprint.mail}</a></dd>
        {/if}
        {if !empty($imprint.telephone)}
            <dt>{lang t="contact|telephone"}</dt>
            <dd><a href="tel:{$imprint.telephone|replace:' ':''}">{$imprint.telephone}</a></dd>
        {/if}
        {if !empty($imprint.mobile_phone)}
            <dt>{lang t="contact|mobile_phone"}</dt>
            <dd><a href="tel:{$imprint.mobile_phone|replace:' ':''}">{$imprint.mobile_phone}</a></dd>
        {/if}
        {if !empty($imprint.fax)}
            <dt>{lang t="contact|fax"}</dt>
            <dd>{$imprint.fax}</dd>
        {/if}
        {if !empty($imprint.vat_id)}
            <dt>{lang t="contact|vat_id"}</dt>
            <dd>{$imprint.vat_id}</dd>
        {/if}
        {if !empty($imprint.disclaimer)}
            <dt>{lang t="contact|disclaimer"}</dt>
            <dd>{$imprint.disclaimer|rewrite_uri}</dd>
        {/if}
        {if !empty($imprint.picture_credits)}
            <dt>{lang t="contact|picture_credits"}</dt>
            <dd>{$imprint.picture_credits|rewrite_uri}</dd>
        {/if}
    </dl>
    <p class="powered-by text-center">
        {$powered_by}
    </p>
{/block}
