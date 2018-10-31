<section id="comments">
    <h3 class="text-center">{lang t="comments|comments"}</h3>
    {redirect_message}
    {if !empty($comments)}
        {include file="asset:System/Partials/pagination.tpl" pagination=$pagination}
        {foreach $comments as $row}
            <blockquote class="blockquote text-center w-75 mx-auto mb-3">
                {$row.message|nl2p}
                <footer class="blockquote-footer">
                    {if !is_null($row.user_id)}
                        <a href="{uri args="users/index/view_profile/id_`$row.user_id`"}"
                           title="{lang t="users|view_profile"}">
                            {$row.name}
                        </a>
                    {else}
                        {$row.name}
                    {/if}
                    <time class="small" datetime="{date_format date=$row.date format="c"}">
                        {date_format date=$row.date format=$dateformat}
                    </time>
                </footer>
            </blockquote>
        {/foreach}
    {else}
        {include file="asset:System/Partials/no_results.tpl"}
    {/if}
</section>
