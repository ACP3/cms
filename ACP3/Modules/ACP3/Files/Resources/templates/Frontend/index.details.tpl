{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <article>
        <section class="dataset-box">
            <header class="navbar navbar-default">
                <div class="navbar-header">
                    <h3 class="navbar-brand">{$file.title}</h3>
                </div>
                <time class="navbar-text small pull-right" datetime="{date_format date=$file.start format="c"}">
                    {date_format date=$file.start format=$dateformat}
                </time>
            </header>
            <div class="content">
                {$file.text|rewrite_uri}
                <footer class="well well-sm hyperlink">
                    <a href="{uri args="files/index/download/id_`$file.id`"}" class="download-file">
                        <i class="fa fa-download" aria-hidden="true"></i>
                        {lang t="files|download_file"}
                        {if !empty($file.size)}
                            ({$file.size})
                        {else}
                            ({lang t="files|unknown_filesize"})
                        {/if}
                    </a>
                </footer>
            </div>
        </section>
        {if $comments_allowed === true}
            <section>
                {load_module module="frontend/comments" args=['module' => 'files', 'entryId' => $file.id]}
                {load_module module="frontend/comments/index/create" args=['module' => 'files', 'entryId' => $file.id, 'redirectUrl' => base64_encode("files/index/details/id_`$file.id`")]}
            </section>
        {/if}
    </article>
{/block}
