{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {include file="asset:System/Partials/ajax-modal.tpl" modal=['id' => 'js-modal-create', 'title' => {lang t="guestbook|create"}]}
    <p class="text-center">
        <a href="{uri args="guestbook/index/create"}" id="js-modal-create-link" title="{lang t="guestbook|create"}">{lang t="guestbook|create"}</a>
    </p>
    {redirect_message}
    {if !empty($guestbook)}
        {include file="asset:System/Partials/pagination.tpl" pagination=$pagination}
        {foreach $guestbook as $row}
            <article id="gb-entry-{$row.id}" class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>
                        {if $row.user_id !== null}
                            <a href="{uri args="users/index/view_profile/id_`$row.user_id`"}" title="{lang t="users|view_profile"}">{$row.name}</a>
                        {else}
                            {$row.name}
                        {/if}
                    </strong>
                    <div>
                        <time class="badge bg-primary rounded-pill" datetime="{date_format date=$row.date format="c"}">{date_format date=$row.date format=$dateformat}</time>
                    </div>
                </div>
                <div class="card-body">
                    {$row.message|decorate}
                </div>
            </article>
        {/foreach}
        {include file="asset:System/Partials/pagination.tpl" pagination=$pagination}
    {else}
        {include file="asset:System/Partials/no_results.tpl"}
    {/if}
{/block}
