<header>
    <h1 class="text-center">{lang t="comments|comments"}</h1>
</header>
{redirect_message}
{if isset($comments)}
    {$pagination}
    {foreach $comments as $row}
        <article class="dataset-box" style="width:65%">
            <header class="navbar navbar-default">
                <div class="navbar-header">
                    <strong class="navbar-brand">
                        {if !is_null($row.user_id)}
                            <a href="{uri args="users/index/view_profile/id_`$row.user_id`"}" title="{lang t="users|view_profile"}">{$row.name}</a>
                        {else}
                            {$row.name}
                        {/if}
                    </strong>
                </div>
                <time class="navbar-text small pull-right" datetime="{date_format date=$row.date format="c"}">{date_format date=$row.date format=$dateformat}</time>
            </header>
            <div class="content">
                {$row.message|nl2p}
            </div>
        </article>
    {/foreach}
{else}
    <div class="alert alert-warning text-center">
        <strong>{lang t="system|no_entries"}</strong>
    </div>
{/if}