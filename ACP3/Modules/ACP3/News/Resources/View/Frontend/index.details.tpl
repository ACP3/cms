{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <article>
        <section class="dataset-box">
            <header class="navbar navbar-default">
                <div class="navbar-header">
                    <h2 class="navbar-brand">{$news.title}</h2>
                </div>
                <time class="navbar-text small pull-right" datetime="{date_format date=$news.start format="c"}">{date_format date=$news.start format=$dateformat}</time>
            </header>
            <div class="content">
                {$news.text|rewrite_uri}
            </div>
            {if $news.uri != '' && $news.link_title != ''}
                <footer>
                    <div class="hyperlink">
                        <strong>{lang t="news|hyperlink"}:</strong>
                        <a href="{$news.uri|prefix_uri}"{$news.target}>{$news.link_title}</a>
                    </div>
                </footer>
            {/if}
        </section>
        {if $comments_allowed === true}
            <section id="comments">
                {load_module module="frontend/comments/" args=['module' => 'news', 'entryId' => $news.id]}
                {load_module module="frontend/comments/index/create" args=['module' => 'news', 'entryId' => $news.id]}
            </section>
        {/if}
    </article>
{/block}
