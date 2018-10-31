{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if $overlay == 1}
        <div class="modal fade" id="modal-create" tabindex="-1" role="dialog" aria-labelledby="modal-create" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                </div>
            </div>
        </div>
        <p class="text-center">
            <a href="{uri args="guestbook/index/create"}"
               id="create-link"
               title="{lang t="guestbook|create"}"
               data-toggle="modal"
               data-target="#modal-create">{lang t="guestbook|create"}</a>
        </p>
        {javascripts}
            {include_js module="guestbook" file="frontend/index.index"}
        {/javascripts}
    {else}
        <p class="text-center">
            <a href="{uri args="guestbook/index/create"}"
               id="create-link"
               title="{lang t="guestbook|create"}">{lang t="guestbook|create"}</a>
        </p>
    {/if}
    {redirect_message}
    {if !empty($guestbook)}
        {include file="asset:System/Partials/pagination.tpl" pagination=$pagination}
        {foreach $guestbook as $row}
            <blockquote class="blockquote text-center">
                {$row.message|nl2p}
                <footer class="blockquote-footer">
                    <strong class="navbar-text">
                        {if !is_null($row.user_id)}
                            <a href="{uri args="users/index/view_profile/id_`$row.user_id`"}" title="{lang t="users|view_profile"}">{$row.name}</a>
                        {else}
                            {$row.name}
                        {/if}
                    </strong>
                    {if $row.website != ''}
                        <a href="{$row.website|prefix_uri}"
                           target="_blank"
                           rel="noopener nofollow"
                           title="{lang t="guestbook|visit_website"}">
                            <i class="fas fa-link"></i>
                        </a>
                    {/if}
                    {if $row.mail != ''}
                        {mailto address=$row.mail encode="javascript" text='<i class="fas fa-envelope"></i>'}
                    {/if}
                    <time class="navbar-text small pull-right" datetime="{date_format date=$row.date format="c"}">
                        {date_format date=$row.date format=$dateformat}
                    </time>
                </footer>
            </blockquote>
        {/foreach}
        {include file="asset:System/Partials/pagination.tpl" pagination=$pagination}
    {else}
        {include file="asset:System/Partials/no_results.tpl"}
    {/if}
{/block}
