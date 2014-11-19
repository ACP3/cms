{extends file="asset:layout.tpl"}

{block CONTENT prepend}
    <div class="modal static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h4 class="modal-title">{$confirm.text}</h4>
                </div>
                <form action="{$confirm.forward}" method="post">
                    <div class="modal-footer">
                        <div class="text-center">
                            {foreach $confirm.data as $key => $value}
                                {if is_array($value)}
                                    {foreach $value as $key2 => $value2}
                                        <input type="hidden" name="{$key}[{$key2}]" value="{$value2}">
                                    {/foreach}
                                {else}
                                    <input type="hidden" name="{$key}" value="{$value}">
                                {/if}
                            {/foreach}
                            {if isset($confirm.backward)}
                                <button type="submit" name="submit" class="btn btn-primary">{lang t="system|yes"}</button>
                                <a href="{$confirm.backward}" class="btn btn-default">{lang t="system|no"}</a>
                            {else}
                                <button type="submit" name="submit" class="btn btn-primary">{lang t="system|forward"}</button>
                            {/if}
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
{/block}