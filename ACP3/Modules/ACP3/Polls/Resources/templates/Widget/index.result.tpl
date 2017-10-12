<section class="panel panel-default">
    <header class="panel-heading">
        <h2 class="panel-title">{lang t="polls|latest_poll"}</h2>
    </header>
    <div class="panel-body">
        <h3 class="h5">{$question}</h3>
        {foreach $answers as $row}
            <strong>{$row.text}</strong>
            <span class="pull-right">{$row.votes}</span>
            <div class="progress active">
                <div class="progress-bar" role="progressbar" aria-valuenow="{$row.percent}" aria-valuemin="0" aria-valuemax="100" style="width:{$row.percent}%"></div>
            </div>
        {/foreach}
        <div class="list-group" style="margin-bottom: 0">
            <a href="{uri args="polls"}" class="list-group-item">{lang t="polls|polls_archive"}</a>
        </div>
    </div>
</section>
