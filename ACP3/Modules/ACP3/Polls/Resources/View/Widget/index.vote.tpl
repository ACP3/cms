<div class="card mb-3">
    <div class="card-header">
        {lang t="polls|latest_poll"}
    </div>
    {if !empty($sidebar_polls)}
        <div class="card-body">
            <h5 class="card-title">{$sidebar_polls.title}</h5>

            <form action="{uri args="polls/index/vote/id_`$sidebar_polls.id`"}" method="post" accept-charset="UTF-8">
                <div class="mb-3">
                    {foreach $sidebar_poll_answers as $row}
                        <div class="form-check">
                            {if $sidebar_polls.multiple == '1'}
                                <input type="checkbox" class="form-check-input" name="answer[]" id="answer-{$row.id}-sidebar" value="{$row.id}">
                            {else}
                                <input type="radio" class="form-check-input" name="answer" id="answer-{$row.id}-sidebar" value="{$row.id}">
                            {/if}
                            <label for="answer-{$row.id}-sidebar" class="form-check-label">
                                {$row.text}
                            </label>
                        </div>
                    {/foreach}
                </div>
                <button type="submit" name="submit" class="btn btn-outline-primary">{lang t="system|submit"}</button>
            </form>
        </div>
    {else}
        <ul class="list-group list-group-flush">
            <li class="list-group-item">{lang t="system|no_entries_short"}</li>
        </ul>
    {/if}
</div>
