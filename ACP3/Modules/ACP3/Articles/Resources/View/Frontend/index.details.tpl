{extends file="asset:`$LAYOUT`"}

{if !empty($page.subtitle)}
    {block PAGE_TITLE}
        {$smarty.block.parent}
        <p class="fs-5 mb-2">{$page.subtitle}</p>
    {/block}
{/if}

{block CONTENT}
    {$page.toc}
    {$page.text|rewrite_uri}
    {event name="articles.event.article_details_after" id=$page.id title=$page.title}
    {if !empty($page.next) || !empty($page.previous)}
        <nav aria-label="{lang t="system|pagination"}">
            <ul class="pagination justify-content-center">
                {if !empty($page.previous)}
                    <li class="page-item">
                        <a href="{$page.previous}" rel="prev" class="page-link">&laquo; {lang t="system|previous_page"}</a>
                    </li>
                {/if}
                {if !empty($page.next)}
                    <li class="page-item">
                        <a href="{$page.next}" rel="next" class="page-link">{lang t="system|next_page"} &raquo;</a>
                    </li>
                {/if}
            </ul>
        </nav>
    {/if}
    {event name="share.layout.add_social_sharing"}
{/block}
