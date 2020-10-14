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
                        <i class="fas fa-download"></i>
                        {lang t="files|download_file"}
                        {if !empty($file.size)}
                            ({$file.size})
                        {else}
                            ({lang t="files|unknown_filesize"})
                        {/if}
                    </a>
                </footer>
            </div>
            {event name="share.layout.add_social_sharing"}
        </section>
        {event name="files.layout.details_after" file=$file}
    </article>
{/block}
