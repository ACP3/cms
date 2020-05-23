<ul class="nav nav-list">
    <li class="nav-header">{lang t="contact|contact"}</li>
    {if !empty($contact.address)}
        <li>{$contact.address|rewrite_uri}</li>
    {/if}
    {if !empty($contact.mail)}
        <li>
            <strong>{lang t="system|email_address"}</strong>
            <a href="{uri args="contact"}">{$contact.mail}</a>
        </li>
    {/if}
    {if !empty($contact.telephone)}
        <li>
            <strong>{lang t="contact|telephone"}</strong>
            <a href="tel:{$contact.telephone|replace:' ':''}">{$contact.telephone}</a>
        </li>
    {/if}
    {if !empty($contact.mobile_phone)}
        <li>
            <strong>{lang t="contact|mobile_phone"}</strong>
            <a href="tel:{$contact.mobile_phone|replace:' ':''}">{$contact.mobile_phone}</a>
        </li>
    {/if}
    {if !empty($contact.fax)}
        <li>
            <strong>{lang t="contact|fax"}</strong>
            {$contact.fax}
        </li>
    {/if}
</ul>
