{extends file="asset:System/Partials/form_group.submit.tpl"}

{block FORM_GROUP_FORM_FIELD_SUBMIT}
    <div class="btn-group">
        <button type="submit"
                name="submit"
                class="btn{if !empty($submit_btn_class)} {$submit_btn_class}{else} btn-primary{/if}">
            {lang t="system|save_and_close"}
        </button>
        <button type="button"
                class="btn{if !empty($submit_btn_class)} {$submit_btn_class}{else} btn-primary{/if} dropdown-toggle dropdown-toggle-split"
                data-bs-toggle="dropdown"
                aria-expanded="false">
            <span class="visually-hidden">{lang t="system|toggle_dropdown"}</span>
        </button>
        <ul class="dropdown-menu">
            <li>
                <button type="submit" name="continue" class="dropdown-item">
                    {lang t="system|save_and_continue"}
                </button>
            </li>
        </ul>
    </div>
{/block}
