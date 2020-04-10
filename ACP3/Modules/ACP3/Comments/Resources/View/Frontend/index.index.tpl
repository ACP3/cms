<section id="comments">
    <h3 class="text-center">{lang t="comments|comments"}</h3>
    {redirect_message}
    {if !empty($comments)}
        {include file="asset:System/Partials/pagination.tpl" pagination=$pagination}
        {foreach $comments as $row}
            <article class="dataset-box dataset-box__comments">
                <header class="navbar navbar-default">
                    <div class="navbar-header">
                        <strong class="navbar-text">
                            {if !is_null($row.user_id)}
                                <a href="{uri args="users/index/view_profile/id_`$row.user_id`"}"
                                   title="{lang t="users|view_profile"}">
                                    {$row.name}
                                </a>
                            {else}
                                {$row.name}
                            {/if}
                        </strong>
                    </div>
                    <time class="navbar-text small pull-right" datetime="{date_format date=$row.date format="c"}">
                        {date_format date=$row.date format=$dateformat}
                    </time>
                </header>
                <div class="content">
                    {$row.message|decorate}
                </div>
            </article>
        {/foreach}
    {else}
        {include file="asset:System/Partials/no_results.tpl"}
    {/if}
</section>
