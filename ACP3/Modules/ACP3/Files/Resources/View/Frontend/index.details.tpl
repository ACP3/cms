{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <article>
        <time class="navbar-text small pull-right" datetime="{date_format date=$file.start format="c"}">
            {date_format date=$file.start format=$dateformat}
        </time>
        {$file.text|rewrite_uri}
        <footer class="card">
            <div class="card-body p-2">
                <a href="{uri args="files/index/download/id_`$file.id`"}">
                    <i class="fas fa-download"></i>
                    {lang t="files|download_file"}
                    {if !empty($file.size)}
                        ({$file.size})
                    {else}
                        ({lang t="files|unknown_filesize"})
                    {/if}
                </a>
            </div>
        </footer>
        {event name="share.layout.add_social_sharing"}
        {if $comments_allowed === true}
            <section class="pt-3">
                {load_module module="frontend/comments" args=['module' => 'files', 'entryId' => $file.id]}
                {load_module module="frontend/comments/index/create" args=['module' => 'files', 'entryId' => $file.id, 'redirectUrl' => base64_encode("files/index/details/id_`$file.id`")]}
            </section>
        {/if}
    </article>
{/block}
