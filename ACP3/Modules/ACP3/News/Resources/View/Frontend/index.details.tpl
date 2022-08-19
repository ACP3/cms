{extends file="asset:`$LAYOUT`"}

{if !empty($news.subtitle)}
    {block PAGE_TITLE}
        {$smarty.block.parent}
        <p class="fs-5 mb-2">{$news.subtitle}</p>
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
