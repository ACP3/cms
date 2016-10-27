{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    <div class="tabbable">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|publication_period"}</a></li>
            <li><a href="#tab-2" data-toggle="tab">{lang t="news|news"}</a></li>
            <li><a href="#tab-3" data-toggle="tab">{lang t="news|hyperlink"}</a></li>
        </ul>
        <div class="tab-content">
            <div id="tab-1" class="tab-pane fade in active">
                {datepicker name=['start', 'end'] value=[$form.start, $form.end]}
            </div>
            <div id="tab-2" class="tab-pane fade">
                {include file="asset:System/Partials/form_group.input_text.tpl" name="title" value=$form.title required=true maxlength=120 data_attributes=['seo-slug-base' => 'true'] label={lang t="news|title"}}
                {include file="asset:System/Partials/form_group.wysiwyg.tpl" name="text" value=$form.text required=true label={lang t="news|text"}}
                <div class="form-group">
                    <label for="cat" class="col-sm-2 control-label required">{lang t="categories|category"}</label>

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
            {event name="seo.layout.render_form_fields" SEO_FORM_FIELDS=$SEO_FORM_FIELDS}
        </div>
    </div>
    {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/news"}}
{/block}
