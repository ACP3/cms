{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <nav id="adm-list" class="navbar navbar-default" role="navigation">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex2-collapse">
                <span class="sr-only">{lang t="system|toggle_navigation"}</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            {block HEADER_BAR_TITLE}
                <span class="navbar-brand">
                    {lang t="system|overview"}
                </span>
            {/block}
        </div>
        <div class="collapse navbar-collapse navbar-ex2-collapse">
            <div class="navbar-text pull-right">
                {block HEADER_BAR_OPTIONS}{/block}
            </div>
        </div>
    </nav>
    {block CONTENT_AFTER_HEADER_BAR}{/block}
{/block}
