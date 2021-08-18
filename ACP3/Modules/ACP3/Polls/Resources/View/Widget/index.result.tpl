<div class="card mb-3">
    <div class="card-header">
        {lang t="polls|latest_poll"}
    </div>
    <div class="card-body">
        <h5 class="card-title">{$sidebar_polls.title}</h5>
        <div class="mb-3">
            {foreach $sidebar_poll_answers as $row}
                <strong>{$row.text}</strong>
                <span class="float-end">{$row.votes}</span>
                <div class="progress mb-1">
                    <div class="progress-bar" role="progressbar" aria-valuenow="{$row.percent}" aria-valuemin="0" aria-valuemax="100" style="width:{$row.percent}%"></div>
                </div>
            {/foreach}
        </div>
        <a href="{uri args="polls"}" class="card-link">{lang t="polls|polls_archive"}</a>
    </div>
</div>
