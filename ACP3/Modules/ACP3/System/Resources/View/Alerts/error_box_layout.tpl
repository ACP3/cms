{if isset($CONTENT_ONLY) && $CONTENT_ONLY === true}
    {block CONTENT}{/block}
    <!-- JAVASCRIPTS -->
{else}
    {include file="asset:`$LAYOUT`" inline}
{/if}
