{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {include file="asset:System/Partials/no_results.tpl" no_results_text={lang t="errors|file_not_found"}}
    <p class="text-center">
        <a href="{uri args=""}" class="btn btn-primary btn-lg">{lang t="errors|go_to_homepage"}</a>
    </p>
{/block}
