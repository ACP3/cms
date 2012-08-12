<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
{if !empty($meta.description)}
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