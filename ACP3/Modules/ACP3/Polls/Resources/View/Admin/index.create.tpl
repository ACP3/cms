{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {tabset identifier="poll-admin-edit-form"}
        {tab title={lang t="system|publication_period"}}
            {datepicker name=['start', 'end'] value=[$form.start, $form.end]}
        {/tab}
        {tab title={lang t="polls|poll"}}
            {include file="asset:System/Partials/form_group.input_text.tpl" name="title" value=$form.title required=true maxlength=120 label={lang t="polls|question"}}
            {foreach $answers as $row}
                {if isset($row.id)}
                    {include file="asset:System/Partials/form_group.input_text.tpl" name="answers[`$row@index`][text]" formFieldId="answer-`$row@index`" value=$row.text required=true maxlength=120 input_group_after="<input type=\"checkbox\" name=\"answers[`$row@index`][delete]\" value=\"1\">" label={lang t="polls|answer_x" args=['%number%' => $row@index+1]}}
                    <input type="hidden" name="answers[{$row@index}][id]" value="{$row.id}">
                {else}
                    {include file="asset:System/Partials/form_group.input_text.tpl" name="answers[`$row@index`][text]" formFieldId="answer-`$row@index`" value=$row.text required=true maxlength=120 label={lang t="polls|answer_x" args=['%number%' => $row@index+1]}}
                {/if}
            {/foreach}
            {include file="asset:System/Partials/form_group.submit.tpl" name="add_answer" submit_btn_class="btn-light" attributes=['data-hash-change' => "#tab-content-2"] submit_label={lang t="polls|add_answer"}}
            {include file="asset:System/Partials/form_group.checkbox.tpl" options=$options label={lang t="system|options"}}
        {/tab}
    {/tabset}
    {include file="asset:System/Partials/form_group.submit_split.tpl" form_token=$form_token back_url={uri args="acp/polls"}}
    {javascripts}
        {include_js module="system" file="partials/hash-change"}
    {/javascripts}
{/block}
