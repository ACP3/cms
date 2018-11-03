{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item"><a href="#tab-1" class="nav-link active" data-toggle="tab">{lang t="system|publication"}</a></li>
        <li class="nav-item"><a href="#tab-2" class="nav-link" data-toggle="tab">{lang t="news|news"}</a></li>
        <li class="nav-item"><a href="#tab-3" class="nav-link" data-toggle="tab">{lang t="news|hyperlink"}</a></li>
    </ul>
    <div class="tab-content">
        <div id="tab-1" class="tab-pane fade show active">
            {include file="asset:System/Partials/form_group.button_group.tpl" options=$active name="active" required=true label={lang t="news|active"}}
            <div id="publication-period-wrapper">
                {datepicker name=['start', 'end'] value=[$form.start, $form.end]}
            </div>
        </div>
        <div id="tab-2" class="tab-pane fade">
            {include file="asset:System/Partials/form_group.input_text.tpl" name="title" value=$form.title required=true maxlength=120 data_attributes=['seo-slug-base' => 'true'] label={lang t="news|title"}}
            {include file="asset:System/Partials/form_group.wysiwyg.tpl" name="text" value=$form.text required=true label={lang t="news|text"}}
            <div class="form-group row">
                <label for="cat" class="col-sm-2 col-form-label required">{lang t="categories|category"}</label>

                <div class="col-sm-10">
                    {include file="asset:Categories/Partials/create_list.tpl" categories=$categories}
                </div>
            </div>
            {if !empty($options)}
                {include file="asset:System/Partials/form_group.checkbox.tpl" options=$options label={lang t="system|options"}}
            {/if}
        </div>
        <div id="tab-3" class="tab-pane fade">
            {include file="asset:System/Partials/form_group.input_text.tpl" name="link_title" value=$form.link_title maxlength=120 label={lang t="news|link_title"}}
            {include file="asset:System/Partials/form_group.input_url.tpl" name="uri" value=$form.uri maxlength=120 label={lang t="news|uri"}}
            {include file="asset:System/Partials/form_group.select.tpl" options=$target label={lang t="news|target_page"}}
        </div>
        {event name="core.layout.form_extension"  uri_pattern=$SEO_URI_PATTERN path=$SEO_ROUTE_NAME}
    </div>
    {include file="asset:System/Partials/form_group.submit_split.tpl" form_token=$form_token back_url={uri args="acp/news"}}
    {javascripts}
        {include_js module="articles" file="admin/index.create"}
    {/javascripts}
{/block}
