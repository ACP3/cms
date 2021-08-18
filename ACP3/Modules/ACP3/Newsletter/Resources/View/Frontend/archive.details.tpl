{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <article class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>{$newsletter.title}</strong>
            <time class="badge bg-primary rounded-pill" datetime="{date_format date=$newsletter.date format="c"}">
                {date_format date=$newsletter.date format="short"}
            </time>
        </div>
        <div class="card-body">
            {$newsletter.text|nl2p}
        </div>
    </article>
    {event name="share.layout.add_social_sharing"}
{/block}
