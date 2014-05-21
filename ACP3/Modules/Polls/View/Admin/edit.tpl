{if isset($error_msg)}
    {$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
    <div class="tabbable">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|publication_period"}</a></li>
            <li><a href="#tab-2" data-toggle="tab">{lang t="polls|poll"}</a></li>
        </ul>
        <div class="tab-content">
            <div id="tab-1" class="tab-pane fade in active">
                {$publication_period}
            </div>
            <div id="tab-2" class="tab-pane fade">
                <div class="form-group">
                    <label for="title" class="col-lg-2 control-label">{lang t="polls|question"}</label>

                    <div class="col-lg-10">
                        <input class="form-control" type="text" name="title" id="title" value="{$title}" maxlength="120">
                    </div>
                </div>
                {foreach $answers as $row}
                    <div class="form-group">
                        <label for="answer-{$row.number}" class="col-lg-2 control-label">{lang t="polls|answer"} {$row.number+1}</label>

                        <div class="col-lg-10">
                            <div class="input-group">
                                <input class="form-control" type="text" name="answers[{$row.number}][value]" id="answer-{$row.number}" value="{$row.value}" maxlength="120">
                                <div class="input-group-addon">
                                    <input type="checkbox" name="answers[{$row.number}][delete]" value="1">
                                </div>
                            </div>
                            <input type="hidden" name="answers[{$row.number}][id]" value="{$row.id}" style="display:none">
                        </div>
                    </div>
                {/foreach}
                <div class="form-group">
                    <label for="{$options.0.name}" class="col-lg-2 control-label">{lang t="system|options"}</label>

                    <div class="col-lg-10">
                        {foreach $options as $row}
                            <div class="checkbox">
                                <label for="{$row.name}">
                                    <input type="checkbox" name="{$row.name}" id="{$row.name}" value="1"{$row.checked}>
                                    {$row.lang}
                                </label>
                            </div>
                        {/foreach}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
            <button type="submit" name="add_answer" class="btn btn-primary">{lang t="polls|add_answer"}</button>
            <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
            <a href="{uri args="acp/polls"}" class="btn btn-default">{lang t="system|cancel"}</a>
            {$form_token}
        </div>
    </div>
</form>