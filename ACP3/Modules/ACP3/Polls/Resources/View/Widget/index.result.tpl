<div class="card mb-3">
    <div class="card-header">
        {lang t="polls|latest_poll"}
    </div>
    <div class="card-body">
        <h5>{$sidebar_polls.title}</h5>
        {foreach $sidebar_poll_answers as $row}
            <strong>{$row.text}</strong>
            <span class="pull-right">{$row.votes}</span>
            <div class="progress active">
                <div class="progress-bar" role="progressbar" aria-valuenow="{$row.percent}" aria-valuemin="0" aria-valuemax="100" style="width:{$row.percent}%"></div>
            </div>
        {/foreach}
    </div>
    <div class="card-footer text-center">
        <a href="{uri args="polls"}" class="card-link">{lang t="polls|polls_archive"}</a>
    </div>
</div>
