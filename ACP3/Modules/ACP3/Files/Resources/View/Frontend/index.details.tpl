{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <article>
        <section class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>{$file.title}</strong>
                <time class="badge bg-primary rounded-pill" datetime="{date_format date=$file.start format="c"}">
                    {date_format date=$file.start format=$dateformat}
                </time>
            </div>
            <div class="card-body">
                {$file.text|rewrite_uri}
            </div>
            <footer class="card-footer">
                <a href="{uri args="files/index/download/id_`$file.id`"}" class="download-file">
                    {icon iconSet="solid" icon="download"}
                    {lang t="files|download_file"}
                    {if !empty($file.size)}
                        ({$file.size})
                    {else}
                        ({lang t="files|unknown_filesize"})
                    {/if}
                </a>
            </footer>
        </section>
        {event name="share.layout.add_social_sharing"}
        {event name="files.layout.details_after" file=$file}
    </article>
{/block}
