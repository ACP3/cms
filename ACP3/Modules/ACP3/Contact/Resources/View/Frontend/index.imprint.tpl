{extends file="asset:`$LAYOUT`"}

{block EDIT_CONTENT}
    {check_access mode="link" path="acp/contact/index/settings/" iconSet="solid" icon="pencil" blank=true selectors="w-100 my-3"}
{/block}

{block CONTENT}
    <dl>
        {if !empty($contact.address)}
            <dt>{lang t="contact|address"}</dt>
            <dd>{$contact.address|rewrite_uri}</dd>
        {/if}
        {if !empty($contact.mail)}
            <dt>{lang t="system|email_address"}</dt>
            <dd><a href="{uri args="contact"}">{$contact.mail}</a></dd>
        {/if}
        {if !empty($contact.telephone)}
            <dt>{lang t="contact|telephone"}</dt>
            <dd><a href="tel:{$contact.telephone|replace:' ':''}">{$contact.telephone}</a></dd>
        {/if}
        {if !empty($contact.mobile_phone)}
            <dt>{lang t="contact|mobile_phone"}</dt>
            <dd><a href="tel:{$contact.mobile_phone|replace:' ':''}">{$contact.mobile_phone}</a></dd>
        {/if}
        {if !empty($contact.fax)}
            <dt>{lang t="contact|fax"}</dt>
            <dd>{$contact.fax}</dd>
        {/if}
        {if !empty($contact.vat_id)}
            <dt>{lang t="contact|vat_id"}</dt>
            <dd>{$contact.vat_id}</dd>
        {/if}
        {if !empty($contact.disclaimer)}
            <dt>{lang t="contact|disclaimer"}</dt>
            <dd>{$contact.disclaimer|rewrite_uri}</dd>
        {/if}
        {if !empty($contact.picture_credits)}
            <dt>{lang t="contact|picture_credits"}</dt>
            <dd>{$contact.picture_credits|rewrite_uri}</dd>
        {/if}
    </dl>
    <p class="border-top pt-2 mb-2 text-center">
        {$powered_by}
    </p>
{/block}
