{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        <div class="form-group">
            <label for="answer-{$answers.0.id}" class="col-sm-2 control-label">{$question}</label>

            <div class="col-sm-10">
                {foreach $answers as $row}
                    {if $multiple == '1'}
                        <div class="checkbox">
                            <label for="answer-{$row.id}">
                                <input type="checkbox" name="answer[]" id="answer-{$row.id}" value="{$row.id}">
                                {$row.text}
                            </label>
                        </div>
                    {else}
                        <div class="radio">
                            <label for="answer-{$row.id}">
                                <input type="radio" name="answer" id="answer-{$row.id}" value="{$row.id}">
                                {$row.text}
                            </label>
                        </div>
                    {/if}
                {/foreach}
            </div>
        </div>
        {include file="asset:System/Partials/form_group.submit.tpl"}
    </form>
    {javascripts}
        {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
