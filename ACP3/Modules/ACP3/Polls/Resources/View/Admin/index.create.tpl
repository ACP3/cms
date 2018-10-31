{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if !empty($error_msg)}
        {$error_msg}
    {else}
        <form action="{$REQUEST_URI}"
              method="post"
              accept-charset="UTF-8"
              id="polls-create-form"
              data-ajax-form="true"
              data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
            <div class="tabbable">
                <ul class="nav nav-tabs mb-3">
                    <li class="nav-item"><a href="#tab-1" class="nav-link active" data-toggle="tab">{lang t="system|publication_period"}</a></li>
                    <li class="nav-item"><a href="#tab-2" class="nav-link" data-toggle="tab">{lang t="polls|poll"}</a></li>
                </ul>
                <div class="tab-content">
                    <div id="tab-1" class="tab-pane fade show active">
                        {datepicker name=['start', 'end'] value=[$form.start, $form.end]}
                    </div>
                    <div id="tab-2" class="tab-pane fade">
                        {include file="asset:System/Partials/form_group.input_text.tpl" name="title" value=$form.title required=true maxlength=120 label={lang t="polls|question"}}
                        {foreach $answers as $row}
                            <div class="form-group row">
                                <label for="answer-{$row@index}" class="col-sm-2 col-form-label">{lang t="polls|answer_x" args=['%number%' => $row@index+1]}</label>

                                <div class="col-sm-10">
                                    {if isset($row.id)}
                                        <div class="input-group">
                                            <input class="form-control" type="text" name="answers[{$row@index}][text]" id="answer-{$row@index}" value="{$row.text}" maxlength="120">
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <input type="checkbox" name="answers[{$row@index}][delete]" value="1">
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="answers[{$row@index}][id]" value="{$row.id}">
                                    {else}
                                        <input class="form-control" type="text" name="answers[{$row@index}][text]" id="answer-{$row@index}" value="{$row.text}" maxlength="120">
                                    {/if}
                                </div>
                            </div>
                        {/foreach}
                        <div class="form-group row">
                            <div class="offset-sm-2 col-sm-10">
                                <button type="submit" name="add_answer" class="btn btn-light" data-hash-change="#tab-2">
                                    {lang t="polls|add_answer"}
                                </button>
                            </div>
                        </div>
                        {include file="asset:System/Partials/form_group.checkbox.tpl" options=$options label={lang t="system|options"}}
                    </div>
                </div>
            </div>
            {include file="asset:System/Partials/form_group.submit_split.tpl" form_token=$form_token back_url={uri args="acp/polls"}}
        </form>
        {javascripts}
        {include_js module="system" file="hash-change"}
        {/javascripts}
    {/if}
{/block}
