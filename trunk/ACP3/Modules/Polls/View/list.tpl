{if isset($polls)}
    <table class="table table-striped">
        <thead>
        <tr>
            <th>{lang t="polls|question"}</th>
            <th>{lang t="polls|votes"}</th>
            <th>{lang t="system|end_date"}</th>
        </tr>
        </thead>
        <tbody>
        {foreach $polls as $row}
            <tr>
                <td><a href="{uri args="polls/`$row.link`/id_`$row.id`"}">{$row.title}</a></td>
                <td>{$row.votes}</td>
                <td>{$row.date}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
{else}
    <div class="alert alert-warning text-center">
        <strong>{lang t="system|no_entries"}</strong>
    </div>
{/if}