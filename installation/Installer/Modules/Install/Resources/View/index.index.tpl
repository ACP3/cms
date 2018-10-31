{extends file="asset:`$LAYOUT`"}

{block CONTENT}
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
    <div class="card bg-light">
        <div class="card-body text-right p-2">
            <a href="{uri args="install/index/licence"}" class="btn btn-primary">
                {lang t="install|forward"}
                <i class="fas fa-chevron-right"></i>
            </a>
        </div>
    </div>
{/block}
