{extends file="asset:`$LAYOUT`"}

{if !empty($page.subtitle)}
    {block PAGE_TITLE}
        {$smarty.block.parent}
        <p class="fs-5 mb-2">{$page.subtitle}</p>
    {/block}
{/if}

{block EDIT_CONTENT}
    {check_access mode="link" path="acp/articles/index/edit/id_`$page.id`/" iconSet="solid" icon="pencil" blank=true selectors="w-100 my-3"}
{/block}

{block CONTENT}
    <div class="clearfix">
        {$page.toc}
        {$page.text|rewrite_uri}
    </div>
    {event name="articles.event.article_details_after" id=$page.id title=$page.title}
    {if !empty($page.next) || !empty($page.previous)}
        {include file="asset:System/Partials/pager.tpl" pager=['next' => $page.next, 'nextLabel' => {lang t="system|next_page"}, 'previous' => $page.previous, 'previousLabel' => {lang t="system|previous_page"}]}
    {/if}
    {event name="share.layout.add_social_sharing"}
{/block}
