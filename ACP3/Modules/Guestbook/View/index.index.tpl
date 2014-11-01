{if $overlay == 1}
    <div class="modal fade" id="modal-create" tabindex="-1" role="dialog" aria-labelledby="modal-create" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            </div>
        </div>
    </div>
    <p class="text-center">
        <a href="{uri args="guestbook/index/create"}" id="create-link" title="{lang t="guestbook|create"}" data-toggle="modal" data-target="#modal-create">{lang t="guestbook|create"}</a>
    </p>
{else}
    <p class="text-center">
        <a href="{uri args="guestbook/index/create"}" id="create-link" title="{lang t="guestbook|create"}">{lang t="guestbook|create"}</a>
    </p>
{/if}
{if isset($redirect_message)}
    {$redirect_message}
{/if}
{if isset($guestbook)}
    {$pagination}
    {foreach $guestbook as $row}
        <article id="gb-entry-{$row.id}" class="dataset-box clearfix" style="width: 65%">
            <header class="navbar navbar-default">
                <div class="navbar-header">
                    <strong class="navbar-brand">
                        {if !empty($row.user_id) && !empty($row.user_id_real)}
                            <a href="{uri args="users/index/view_profile/id_`$row.user_id`"}" title="{lang t="users|view_profile"}">{$row.name}</a>
                        {else}
                            {$row.name}
                        {/if}
                    </strong>
                </div>
                <small class="navbar-text pull-right">
                    <time datetime="{date_format date=$row.date format="c"}">{date_format date=$row.date format=$dateformat}</time>
                </small>
            </header>
            <div class="content">
                <div class="pull-right">
                    {if $row.website != ''}
                        <a href="{$row.website}" rel="nofollow" onclick="window.open(this.href); return false;" title="{lang t="guestbook|visit_website"}">
                            {icon path="16/gohome" width="16" height="16" alt="`$row.website`"}
                        </a>
                        <br>
                    {/if}
                    {if $row.mail != ''}
                        {mailto address=$row.mail encode="javascript" text="{icon path="16/mail" width="16" height="16" alt="`$row.mail`"}"}
                    {/if}
                </div>
                {$row.message}
            </div>
        </article>
    {/foreach}
{else}
    <div class="alert alert-warning text-center">
        <strong>{lang t="system|no_entries"}</strong>
    </div>
{/if}