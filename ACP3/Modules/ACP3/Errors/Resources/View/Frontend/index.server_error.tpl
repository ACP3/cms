{extends file="asset:$LAYOUT"}

{block CONTENT}
    {include file="asset:System/Partials/no_results.tpl" no_results_text={lang t="errors|server_unavailable"}}
    <p class="text-center">
        <a href="{uri args=""}" class="btn btn-primary btn-lg">{lang t="errors|retry"}</a>
    </p>
{/block}
