{if !empty($sidebar_news_latest)}
    <section class="panel panel-default">
        <header class="panel-heading">
            <h2 class="panel-title">
                <a href="{uri args="news/index/details/id_`$sidebar_news_latest.id`"}">
                    {$sidebar_news_latest.title}
                </a>
            </h2>
        </header>
        <div class="panel-body">
            {$sidebar_news_latest.text}
        </div>
    </section>
{/if}
