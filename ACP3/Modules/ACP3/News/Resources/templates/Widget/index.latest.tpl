{if !empty($sidebar_news_latest)}
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                <a href="{uri args="news/index/details/id_`$sidebar_news_latest.id`"}">
                    {$sidebar_news_latest.title}
                </a>
            </h3>
        </div>
        <div class="panel-body">
            {$sidebar_news_latest.text}
        </div>
    </div>
{/if}