<meta charset="UTF-8">{if !empty($meta.description)}
<meta name="description" content="{$meta.description}">
{/if}
{if !empty($meta.keywords)}
    <meta name="keywords" content="{$meta.keywords}">
{/if}
{if !empty($meta.robots)}
    <meta name="robots" content="{$meta.robots}">
{/if}
{if !empty($meta.previous_page)}
    <link rel="prev" href="{$meta.previous_page}">
{/if}
{if !empty($meta.next_page)}
    <link rel="next" href="{$meta.next_page}">
{/if}
{if !empty($meta.canonical)}
    <link rel="canonical" href="{$meta.canonical}">
{/if}