{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    <div class="form-group">
        <label for="answer-{$answers.0.id}" class="col-sm-2 control-label">{$poll.title}</label>

        <div class="col-sm-10">
            {foreach $answers as $row}
                {if $poll.multiple == '1'}
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
{/block}
