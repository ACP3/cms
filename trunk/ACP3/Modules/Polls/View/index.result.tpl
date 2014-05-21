{if isset($redirect_message)}
    {$redirect_message}
{/if}<h4 style="margin-bottom:10px">{$question}</h4>
<table class="table table-striped">
    <thead>
    <tr>
        <th>{lang t="polls|answers"}</th>
        <th>{lang t="polls|per_cent"}</th>
        <th>{lang t="polls|votes"}</th>
    </tr>
    </thead>
    <tfoot>
    <tr>
        <td colspan="2">{lang t="polls|total_votes"}:</td>
        <td>{$total_votes}</td>
    </tr>
    </tfoot>
    <tbody>
    {foreach $answers as $row}
        <tr>
            <td>{$row.text}</td>
            <td>{$row.percent}%</td>
            <td>{$row.votes}</td>
        </tr>
    {/foreach}
    </tbody>
</table><p style="text-align:center">
    <a href="{uri args="polls"}">{lang t="polls|polls_archive"}</a>
</p>