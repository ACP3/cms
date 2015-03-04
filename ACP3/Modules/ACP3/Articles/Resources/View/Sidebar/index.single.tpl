{if !empty($sidebar_article)}
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                <a href="{uri args="articles/index/details/id_`$sidebar_article.id`"}">
                    {$sidebar_article.title}
                </a>
            </h3>
        </div>
        <div class="panel-body">
            {$sidebar_article.text}
        </div>
    </div>
{/if}