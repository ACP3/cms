<section class="panel panel-default">
    <header class="panel-heading">
        <h2 class="panel-title">{lang t="polls|latest_poll"}</h2>
    </header>
    {if !empty($poll)}
        <div class="panel-body">
            <h3 class="h3">{$poll.title}</h3>

            <form action="{uri args="polls/index/vote/id_`$poll.id`"}" method="post" accept-charset="UTF-8">
                {foreach $answers as $row}
                    {if $poll.multiple == '1'}
                        <div class="checkbox">
                            <label for="answer-{$row.id}-sidebar">
                                <input type="checkbox" name="answer[]" id="answer-{$row.id}-sidebar" value="{$row.id}">
                                {$row.text}
                            </label>
                        </div>
                    {else}
                        <div class="radio">
                            <label for="answer-{$row.id}-sidebar">
                                <input type="radio" name="answer" id="answer-{$row.id}-sidebar" value="{$row.id}">
                                {$row.text}
                            </label>
                        </div>
                    {/if}
                {/foreach}
                <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
                {$form_token}
            </form>
        </div>
    {else}
        <div class="list-group">
            <span class="list-group-item">{lang t="system|no_entries_short"}</span>
        </div>
    {/if}
</section>
