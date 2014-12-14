{if !empty($sidebar_article)}
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{$sidebar_article.title}</h3>
        </div>
        <div class="panel-body">
            {$sidebar_article.text}
        </div>
    </div>
{/if}