{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if !empty($polls)}
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
                    <td><a href="{uri args="polls/index/`$row.link`/id_`$row.id`"}">{$row.title}</a></td>
                    <td>{$row.votes}</td>
                    <td>
                        {if $row.start == $row.end}
                            -
                        {else}
                            {date_format date=$row.end}
                        {/if}
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    {else}
        {include file="asset:System/Partials/no_results.tpl"}
    {/if}
{/block}
