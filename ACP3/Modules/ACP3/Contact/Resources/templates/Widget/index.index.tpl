<ul class="nav nav-list">
    <li class="nav-header">{lang t="contact|contact"}</li>
    {if !empty($sidebar_contact.address)}
        <li>{$sidebar_contact.address|rewrite_uri}</li>
    {/if}
    {if !empty($sidebar_contact.mail)}
        <li>
            <strong>{lang t="system|email_address"}</strong>
            <a href="{uri args="contact"}">{$sidebar_contact.mail}</a>
        </li>
    {/if}
    {if !empty($sidebar_contact.telephone)}
        <li>
            <strong>{lang t="contact|telephone"}</strong>
            <a href="tel:{$sidebar_contact.telephone|replace:' ':''}">{$sidebar_contact.telephone}</a>
        </li>
    {/if}
    {if !empty($sidebar_contact.mobile_phone)}
        <li>
            <strong>{lang t="contact|mobile_phone"}</strong>
            <a href="tel:{$sidebar_contact.mobile_phone|replace:' ':''}">{$sidebar_contact.mobile_phone}</a>
        </li>
    {/if}
    {if !empty($sidebar_contact.fax)}
        <li>
            <strong>{lang t="contact|fax"}</strong>
            {$sidebar_contact.fax}
        </li>
    {/if}
</ul>
