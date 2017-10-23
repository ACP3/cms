{extends file="asset:System/Partials/form_group.submit.tpl"}

{block FORM_GROUP_FORM_FIELD_SUBMIT}
    <div class="btn-group">
        <button type="submit"
                name="submit"
                class="btn{if !empty($submit_btn_class)} {$submit_btn_class}{else} btn-primary{/if}">
            {lang t="system|save_and_close"}
        </button>
        <button type="button"
                class="btn{if !empty($submit_btn_class)} {$submit_btn_class}{else} btn-primary{/if} dropdown-toggle"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false">
            <span class="caret"></span>
            <span class="sr-only">{lang t="system|toggle_dropdown"}</span>
        </button>
        <ul class="dropdown-menu">
            <li>
                <button type="submit" name="continue" class="btn btn-link btn-block text-left">
                    {lang t="system|save_and_continue"}
                </button>
            </li>
        </ul>
    </div>
{/block}
