{extends file="asset:layout.tpl"}

{block CONTENT}
    {if isset($files)}
        {foreach $files as $row}
            <div class="dataset-box">
                <div class="navbar navbar-default">
                    <div class="navbar-header">
                        <h2 class="navbar-brand">
                            <a href="{uri args="files/index/details/id_`$row.id`"}">
                                {$row.title}
                                {if !empty($row.size)}
                                    ({$row.size})
                                {else}
                                    ({lang t="files|unknown_filesize"})
                                {/if}
                            </a>
                        </h2>
                    </div>
                    <time class="navbar-text small pull-right" datetime="{date_format date=$row.start format="c"}">{date_format date=$row.start format=$dateformat}</time>
                </div>
            </div>
        {/foreach}
    {else}
        <div class="alert alert-warning text-center">
            <strong>{lang t="system|no_entries"}</strong>
        </div>
    {/if}
{/block}