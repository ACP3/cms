{extends file="asset:System/ajax-form.tpl"}

{block AJAX_FORM_CONTENT}
    <div class="tabbable">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|publication_period"}</a></li>
            <li><a href="#tab-2" data-toggle="tab">{lang t="news|news"}</a></li>
            <li><a href="#tab-3" data-toggle="tab">{lang t="news|hyperlink"}</a></li>
            <li><a href="#tab-4" data-toggle="tab">{lang t="seo|seo"}</a></li>
        </ul>
        <div class="tab-content">
            <div id="tab-1" class="tab-pane fade in active">
                {datepicker name=['start', 'end'] value=[$form.start, $form.end]}
            </div>
            <div id="tab-2" class="tab-pane fade">
                <div class="form-group">
                    <label for="title" class="col-sm-2 control-label required">{lang t="news|title"}</label>

                    <div class="col-sm-10">
                        <input class="form-control"
                               type="text"
                               name="title"
                               id="title"
                               value="{$form.title}"
                               maxlength="120"
                               data-seo-slug-base="true"
                               required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="text" class="col-sm-2 control-label required">{lang t="news|text"}</label>

                    <div class="col-sm-10">{wysiwyg name="text" value="`$form.text`" height="250"}</div>
                </div>
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
            <div id="tab-4" class="tab-pane fade">
                {include file="asset:Seo/Partials/seo_fields.tpl" seo=$SEO_FORM_FIELDS}
            </div>
        </div>
    </div>
    {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/news"}}
{/block}
