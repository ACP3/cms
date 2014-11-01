{if isset($newsletter)}
    <article class="dataset-box">
        <header class="navbar navbar-default">
            <div class="navbar-header">
                <h2 class="navbar-brand">{$newsletter.title}</h2>
            </div>
            <small class="navbar-text pull-right">
                <time datetime="{date_format date=$newsletter.date format="c"}">{date_format date=$newsletter.date format="short"}</time>
            </small>
        </header>
        <div class="content">
            {$newsletter.text|nl2p}
        </div>
    </article>
{else}
    <div class="alert alert-warning text-center">
        <strong>{lang t="newsletter|select_newsletter"}</strong>
    </div>
{/if}