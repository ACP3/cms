{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <section class="modal static">
        <div class="modal-dialog">
            <div class="modal-content">
                <header class="modal-header text-center">
                    <h2 class="modal-title h4">{$confirm.text}</h2>
                </header>
                <div class="modal-footer">
                    <div class="text-center">
                        {block CONFIRM_BOX_MODAL_FOOTER}
                            {if isset($confirm.backward)}
                                <a href="{$confirm.forward}" class="btn btn-primary">{lang t="system|yes"}</a>
                                <a href="{$confirm.backward}" class="btn btn-default">{lang t="system|no"}</a>
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
    </section>
{/block}
