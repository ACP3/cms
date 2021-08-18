{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    <div class="row mb-3">
        <label for="answer-{$answers.0.id}" class="col-sm-2 col-form-label">{$question}</label>

        <div class="col-sm-10">
            {foreach $answers as $row}
                <div class="form-check">
                    {if $multiple == '1'}
                        <input type="checkbox" class="form-check-input" name="answer[]" id="answer-{$row.id}" value="{$row.id}">
                    {else}
                        <input type="radio" class="form-check-input" name="answer" id="answer-{$row.id}" value="{$row.id}">
                    {/if}
                    <label for="answer-{$row.id}" class="form-check-label">
                        {$row.text}
                    </label>
                </div>
            {/foreach}
        </div>
    </div>
    {include file="asset:System/Partials/form_group.submit.tpl"}
{/block}
