{extends file="asset:System/layout.header-bar.tpl"}

{block CONTENT_AFTER_HEADER_BAR}
    <iframe src="{$filemanager_path}"
            class="w-100"
            loading="lazy"
            style="height:500px">
{/block}
