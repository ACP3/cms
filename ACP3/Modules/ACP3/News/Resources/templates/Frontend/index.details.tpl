{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <article>
        <section class="dataset-box">
            <time class="small" datetime="{date_format date=$news.start format="c"}">
                {date_format date=$news.start format=$dateformat}
            </time>
            <div class="dataset-box__content dataset-box__content_detail-page">
                {$news.text|rewrite_uri}
                {if $news.uri != '' && $news.link_title != ''}
                    <footer class="well well-sm dataset-box__hyperlink">
                        <strong>{lang t="news|hyperlink"}:</strong>
                        <a href="{$news.uri|prefix_uri}"{$news.target}>{$news.link_title}</a>
                    </footer>
                {/if}
            </div>
            {event name="news.event.news_details_after" id=$news.id title=$news.title}
        </section>
        {if $comments_allowed === true}
            <section>
                {load_module module="frontend/comments" args=['module' => 'news', 'entryId' => $news.id]}
                {load_module module="frontend/comments/index/create" args=['module' => 'news', 'entryId' => $news.id, 'redirectUrl' => base64_encode("news/index/details/id_`$news.id`")]}
            </section>
        {/if}
    </article>
{/block}
