{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" id="polls-create-form" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        <div class="tabbable">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|publication_period"}</a></li>
                <li><a href="#tab-2" data-toggle="tab">{lang t="polls|poll"}</a></li>
            </ul>
            <div class="tab-content">
                <div id="tab-1" class="tab-pane fade in active">
                    {datepicker name=['start', 'end'] value=[$form.start, $form.end]}
                </div>
                <div id="tab-2" class="tab-pane fade">
                    <div class="form-group">
                        <label for="title" class="col-sm-2 control-label required">{lang t="polls|question"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="title" id="title" value="{$form.title}" maxlength="120" required>
                        </div>
                    </div>
                    {foreach $answers as $row}
                        <div class="form-group">
                            <label for="answer-{$row@index}" class="col-sm-2 control-label">{lang t="polls|answer_x" args=['%number%' => $row@index+1]}</label>

                            <div class="col-sm-10">
                                {if isset($row.id)}
                                    <div class="input-group">
                                        <input class="form-control" type="text" name="answers[{$row@index}][text]" id="answer-{$row@index}" value="{$row.text}" maxlength="120">
                                        <div class="input-group-addon">
                                            <input type="checkbox" name="answers[{$row@index}][delete]" value="1">
                                        </div>
                                    </div>
                                    <input type="hidden" name="answers[{$row@index}][id]" value="{$row.id}" style="display:none">
                                {else}
                                    <input class="form-control" type="text" name="answers[{$row@index}][text]" id="answer-{$row@index}" value="{$row.text}" maxlength="120">
                                {/if}
                            </div>
                        </div>
                    {/foreach}
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" name="add_answer" class="btn btn-default" data-hash-change="#tab-2">
                                {lang t="polls|add_answer"}
                            </button>
                        </div>
                    </div>
                    {include file="asset:System/Partials/form_group.checkbox.tpl" options=$options label={lang t="system|options"}}
                </div>
            </div>
        </div>
        {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/polls"}}
    </form>
    {javascripts}
    {include_js module="system" file="ajax-form"}
    {include_js module="system" file="hash-change"}
    {/javascripts}
{/block}
