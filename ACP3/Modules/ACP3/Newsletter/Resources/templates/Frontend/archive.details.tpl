{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <article class="dataset-box">
        <time class="small" datetime="{date_format date=$newsletter.date format="c"}">
            {date_format date=$newsletter.date format="short"}
        </time>
        <div class="dataset-box__content dataset-box__content_detail-page">
            {$newsletter.text|nl2p}
        </div>
    </article>
{/block}
