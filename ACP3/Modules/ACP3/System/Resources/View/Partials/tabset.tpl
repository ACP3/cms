<div class="tabbable">
    <ul class="nav nav-tabs">
        {foreach $tabset as $tab}
            <li{if $tab@iteration === 1} class="active"{/if}><a href="#tab-{$tab@iteration}" data-toggle="tab">{$tab->getTitle()}</a></li>
        {/foreach}
    </ul>
    <div class="tab-content">
        {foreach $tabset as $tab}
            <div id="tab-{$tab@iteration}" class="tab-pane fade{if $tab@iteration === 1} in active{/if}">
                {$tab->getContent()}
            </div>
        {/foreach}
    </div>
    {$tabset_appendix}
</div>
