{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <nav id="adm-list" class="navbar navbar-expand-lg navbar-light bg-light mb-3">
        {block HEADER_BAR_TITLE}
            <span class="navbar-brand">
                {lang t="system|overview"}
            </span>
        {/block}
        <button class="navbar-toggler"
                type="button"
                data-toggle="collapse"
                data-target="#header-bar-collapse"
                aria-controls="header-bar-collapse"
                aria-expanded="false"
                aria-label="{lang t="system|toggle_navigation"}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="header-bar-collapse">
            <div class="navbar-text ml-auto">
                {block HEADER_BAR_OPTIONS}{/block}
            </div>
        </div>
    </nav>
    {block CONTENT_AFTER_HEADER_BAR}{/block}
{/block}
