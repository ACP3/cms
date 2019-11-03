{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <p>
        {lang t="installer|installation_successful_1"}
    </p>
    {include file="asset:Installer/Partials/delete_installer_directory.tpl"}
    {include file="asset:Installer/Partials/next_steps.tpl"}
{/block}
