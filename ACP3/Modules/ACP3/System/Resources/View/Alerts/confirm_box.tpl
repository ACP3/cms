{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <div class="modal modal-static" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-center">{$confirm.text}</h5>
                </div>
                <div class="modal-footer text-center">
                    {block CONFIRM_BOX_MODAL_FOOTER}
                        {if isset($confirm.backward)}
                            <a href="{$confirm.forward}" class="btn btn-primary">{lang t="system|yes"}</a>
                            <a href="{$confirm.backward}" class="btn btn-light">{lang t="system|no"}</a>
                        {elseif $confirm.overlay == 1}
                            <a href="javascript:window.parent.$.fancybox.close();window.parent.location.reload();" class="btn btn-primary">{lang t="system|close_window"}</a>
                        {elseif is_array($confirm.forward)}
                            <a href="{$confirm.forward.url}" class="btn btn-primary">{$confirm.forward.lang}</a>
                        {else}
                            <a href="{$confirm.forward}" class="btn btn-primary">{lang t="system|forward"}</a>
                        {/if}
                    {/block}
                </div>
            </div>
        </div>
    </div>
{/block}
