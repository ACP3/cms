{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    <div class="form-group row">
        <label for="answer-{$answers.0.id}" class="col-sm-2 col-form-label pt-0">{$question}</label>

        <div class="col-sm-10">
            {foreach $answers as $row}
                {if $multiple == '1'}
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="answer[]" id="answer-{$row.id}" value="{$row.id}">
                        <label for="answer-{$row.id}" class="form-check-label">
                            {$row.text}
                        </label>
                    </div>
                {else}
                    <div class="form-check">
                        <input type="radio" class="form-check-input" name="answer" id="answer-{$row.id}" value="{$row.id}">
                        <label for="answer-{$row.id}" class="form-check-label">
                            {$row.text}
                        </label>
                    </div>
                {/if}
            {/foreach}
        </div>
    </div>
    {include file="asset:System/Partials/form_group.submit.tpl"}
{/block}
