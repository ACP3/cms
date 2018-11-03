{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <article>
        <time class="text-muted small" datetime="{date_format date=$news.start format="c"}">
            {date_format date=$news.start format=$dateformat}
        </time>
        {$news.text|rewrite_uri}
        {if $news.uri != '' && $news.link_title != ''}
            <footer class="card bg-light">
                <div class="card-body p-2">
                    <strong>{lang t="news|hyperlink"}:</strong>
                    <a href="{$news.uri|prefix_uri}"{$news.target}>{$news.link_title}</a>
                </div>
            </footer>
        {/if}
        {event name="news.event.news_details_after" id=$news.id title=$news.title}
        {event name="share.layout.add_social_sharing"}
        {if $comments_allowed === true}
            <section class="pt-3">
                {load_module module="frontend/comments" args=['module' => 'news', 'entryId' => $news.id]}
                {load_module module="frontend/comments/index/create" args=['module' => 'news', 'entryId' => $news.id, 'redirectUrl' => base64_encode("news/index/details/id_`$news.id`")]}
            </section>
        {/if}
    </article>
{/block}
