{extends file="asset:layout.tpl"}

{block CONTENT prepend}
    <p>
        {lang t="install|step_1_paragraph_1"}
    </p>
    <fieldset>
        <legend>{lang t="install|step_1_legend"}</legend>
        <ol>
            <li>{lang t="install|step_1_list_item_1"}</li>
            <li>{lang t="install|step_1_list_item_2"}</li>
            <li>{lang t="install|step_1_list_item_3"}</li>
            <li>{lang t="install|step_1_list_item_4"}</li>
        </ol>
    </fieldset>
    <div class="well well-sm text-center">
        <a href="{uri args="install/index/licence"}" class="btn btn-default">{lang t="install|forward"}</a>
    </div>
{/block}