{extends file="asset:`$LAYOUT`"}

{if !empty($page.subtitle)}
    {block PAGE_TITLE}
        <h2>
            {page_title}<br>
            <small>{$page.subtitle}</small>
        </h2>
    {/block}
{/if}

{block CONTENT}
    {$page.toc}
    {$page.text|rewrite_uri}
    {event name="articles.event.article_details_after" id=$page.id title=$page.title}
    {if !empty($page.next) || !empty($page.previous)}
        <div class="row">
            {if !empty($page.previous)}
                <div class="col">
                    <a href="{$page.previous}" rel="prev" class="btn btn-outline-primary">&laquo; {lang t="system|previous_page"}</a>
                </div>
            {/if}
            {if !empty($page.next)}
                <div class="col text-right">
                    <a href="{$page.next}" rel="next" class="btn btn-outline-primary">{lang t="system|next_page"} &raquo;</a>
                </div>
            {/if}
        </div>
    {/if}
    {event name="share.layout.add_social_sharing"}
{/block}
