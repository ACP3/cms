{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <article>
        <time class="text-muted small" datetime="{date_format date=$newsletter.date format="c"}">
            {date_format date=$newsletter.date format="short"}
        </time>
        {$newsletter.text|rewrite_uri}
        {event name="share.layout.add_social_sharing"}
    </article>
{/block}
