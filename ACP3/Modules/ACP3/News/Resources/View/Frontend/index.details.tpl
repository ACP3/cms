{extends file="asset:`$LAYOUT`"}

{if !empty($news.subtitle)}
    {block PAGE_TITLE}
        <h2 itemprop="name">
            {page_title}<br>
            <small class="fs-5">{$news.subtitle}</small>
        </h2>
    {/block}
{/if}

{block CONTENT}
    <time class="text-muted d-block mb-3" datetime="{date_format date=$news.start format="c"}">
        {date_format date=$news.start format=$dateformat}
    </time>
    <div class="mb-3">
        {$news.text|rewrite_uri}
    </div>
    {if $news.uri != '' && $news.link_title != ''}
        <div class="list-group mb-3">
            <a href="{$news.uri|prefix_uri}" class="list-group-item list-group-item-action"{$news.target}>
                {$news.link_title}
            </a>
        </div>
    {/if}
    {event name="share.layout.add_social_sharing"}
    {event name="news.layout.details_after" news=$news}
{/block}
