<ul class="nav nav-list">
    <li class="nav-header">{lang t="contact|contact"}</li>
    {if !empty($sidebar_contact.address)}
        <li>{$sidebar_contact.address}</li>
    {/if}
    {if !empty($sidebar_contact.mail)}
        <li>
            <strong>{lang t="system|email_address"}</strong> <a href="{uri args="contact"}">{$sidebar_contact.mail}</a>
        </li>
    {/if}
    {if !empty($sidebar_contact.telephone)}
        <li>
            <strong>{lang t="contact|telephone"}</strong>
            {$sidebar_contact.telephone}
        </li>
    {/if}
    {if !empty($sidebar_contact.fax)}
        <li>
            <strong>{lang t="contact|fax"}</strong>
            {$sidebar_contact.fax}
        </li>
    {/if}
</ul>