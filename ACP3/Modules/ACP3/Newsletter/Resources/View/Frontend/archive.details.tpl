{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <article class="dataset-box">
        <header class="navbar navbar-default">
            <div class="navbar-header">
                <h3 class="navbar-brand">{$newsletter.title}</h3>
            </div>
            <time class="navbar-text small pull-right" datetime="{date_format date=$newsletter.date format="c"}">
                {date_format date=$newsletter.date format="short"}
            </time>
        </header>
        <div class="content">
            {$newsletter.text|nl2p}
        </div>
        {event name="share.layout.add_social_sharing"}
    </article>
{/block}
