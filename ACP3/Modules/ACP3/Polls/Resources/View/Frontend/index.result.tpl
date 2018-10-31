{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {redirect_message}
    <h3>{$question}</h3>
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
            <th colspan="2">{lang t="polls|total_votes"}:</th>
            <th>{$total_votes}</th>
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
    </table>
    <p class="text-center">
        <a href="{uri args="polls"}" class="btn btn-light">{lang t="polls|polls_archive"}</a>
    </p>
{/block}
