{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {$page.toc}
    {$page.text|rewrite_uri}
    {event name="articles.event.article_details_after" id=$page.id title=$page.title}
    {if !empty($page.next) || !empty($page.previous)}
        <ul class="pager" style="clear:both">
            {if !empty($page.previous)}
                <li class="previous">
                    <a href="{$page.previous}" rel="prev" class="previous">&laquo; {lang t="system|previous_page"}</a>
                </li>
            {/if}
            {if !empty($page.next)}
                <li class="next">
                    <a href="{$page.next}" rel="next" class="next">{lang t="system|next_page"} &raquo;</a>
                </li>
            {/if}
        </ul>
    {/if}
{/block}
