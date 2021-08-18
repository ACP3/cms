{if !empty($sidebar_news_latest)}
    <div class="card mb-3">
        <div class="card-header">
            <a href="{uri args="news/index/details/id_`$sidebar_news_latest.id`"}">
                {$sidebar_news_latest.title}
            </a>
        </div>
        <div class="card-body">
            {$sidebar_news_latest.text}
        </div>
    </div>
{/if}
