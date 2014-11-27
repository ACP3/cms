{extends file="asset:layout.tpl"}

{block CONTENT}
    <article>
        <section class="dataset-box">
            <header class="navbar navbar-default">
                <div class="navbar-header">
                    <h2 class="navbar-brand">{$file.title}</h2>
                </div>
                <small class="navbar-text pull-right">
                    <time datetime="{date_format date=$file.start format="c"}">{date_format date=$file.start format=$dateformat}</time>
                </small>
            </header>
            <div class="content">
                {$file.text}
            </div>
            <footer class="hyperlink">
                <a href="{uri args="files/index/details/id_`$file.id`/action_download"}" class="download-file">
                    <i class="glyphicon glyphicon-download-alt"></i>
                    {lang t="files|download_file"}
                    {if !empty($file.size)}
                        ({$file.size})
                    {else}
                        ({lang t="files|unknown_filesize"})
                    {/if}
                </a>
            </footer>
        </section>
        {if isset($comments)}
            {$comments}
        {/if}
    </article>
{/block}