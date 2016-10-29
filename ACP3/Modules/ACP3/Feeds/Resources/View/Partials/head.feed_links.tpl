{foreach $available_feeds as $feed}
    <link rel="alternate"
          type="application/rss+xml"
          href="{uri args="feeds/index/index/feed_`$feed`"}"
          title="{site_title} - {lang t="`$feed`|`$feed`"}">
{/foreach}
