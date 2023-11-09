<div class="card mb-3">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs">
            {foreach $tabset as $tab}
                <li class="nav-item"><a href="#tab-content-{($tab->getName()) ? $tab->getName() : $tab@iteration}" id="tab-{($tab->getName()) ? $tab->getName() : $tab@iteration}" class="nav-link{if $tab@iteration === 1} active{/if}" data-bs-toggle="tab">{$tab->getTitle()}</a></li>
            {/foreach}
        </ul>
    </div>
    <div class="card-body tab-content">
        {foreach $tabset as $tab}
            <div id="tab-content-{($tab->getName()) ? $tab->getName() : $tab@iteration}" class="tab-pane fade{if $tab@iteration === 1} show active{/if}" role="tabpanel" aria-labelledby="tab-{($tab->getName()) ? $tab->getName() : $tab@iteration}">
                {$tab->getContent()}
            </div>
        {/foreach}
    </div>
    {$tabset_appendix}
</div>
