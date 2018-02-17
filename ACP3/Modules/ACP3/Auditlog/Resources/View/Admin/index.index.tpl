{extends file="asset:System/layout.header-bar.tpl"}

{block CONTENT_AFTER_HEADER_BAR}
    {redirect_message}
    {include file="asset:System/Partials/no_results.tpl" no_results_text={lang t="auditlog|nothing_to_see_here_yet"}}
{/block}
