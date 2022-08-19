{extends file="asset:`$LAYOUT`"}

{if !empty($news.subtitle)}
    {block PAGE_TITLE}
        <h2 itemprop="name">
            {page_title}<br>
            <small>{$news.subtitle}</small>
        </h2>
    {/block}
{/if}

{block CONTENT}
    <article>
        <section class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>{$news.title}</strong>
                <time class="badge bg-primary rounded-pill" datetime="{date_format date=$news.start format="c"}">
                    {date_format date=$news.start format=$dateformat}
                </time>
            </div>
            <div class="card-body">
                {$news.text|rewrite_uri}
            </div>
            {if $news.uri != '' && $news.link_title != ''}
                <footer class="card-footer">
                    <strong>{lang t="news|hyperlink"}:</strong>
                    <a href="{$news.uri|prefix_uri}"{$news.target}>{$news.link_title}</a>
                </footer>
            {/if}
        </section>
        {event name="share.layout.add_social_sharing"}
        {event name="news.layout.details_after" news=$news}
    </article>
{/block}
