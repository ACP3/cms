{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <article class="dataset-box">
        <header class="navbar navbar-default">
            <div class="navbar-header">
                <h2 class="navbar-brand">{$newsletter.title}</h2>
            </div>
            <time class="navbar-text small pull-right" datetime="{date_format date=$newsletter.date format="c"}">
                {date_format date=$newsletter.date format="short"}
            </time>
        </header>
        <div class="content">
            {$newsletter.text|nl2p}
        </div>
    </article>
{/block}
