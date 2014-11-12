{extends file="asset:layout.tpl"}

{block CONTENT prepend}
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
            <dd>{$imprint.telephone}</dd>
        {/if}
        {if !empty($imprint.fax)}
            <dt>{lang t="contact|fax"}</dt>
            <dd>{$imprint.fax}</dd>
        {/if}
        {if !empty($imprint.disclaimer)}
            <dt>{lang t="contact|disclaimer"}</dt>
            <dd>{$imprint.disclaimer|rewrite_uri}</dd>
        {/if}
    </dl>
    <p class="powered-by text-center">
        {$powered_by}
    </p>
{/block}