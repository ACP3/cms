{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <p>
        {lang t="installer|step_1_paragraph_1"}
    </p>
    <fieldset>
        <legend>{lang t="installer|step_1_legend"}</legend>
        <ol>
            <li>{lang t="installer|step_1_list_item_1"}</li>
            <li>{lang t="installer|step_1_list_item_2"}</li>
            <li>{lang t="installer|step_1_list_item_3"}</li>
            <li>{lang t="installer|step_1_list_item_4"}</li>
        </ol>
    </fieldset>
    <div class="well well-sm text-center">
        <a href="{uri args="installer/index/licence"}" class="btn btn-default">{lang t="installer|forward"}</a>
    </div>
{/block}
