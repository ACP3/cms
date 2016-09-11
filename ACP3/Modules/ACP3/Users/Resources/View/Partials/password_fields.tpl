{if empty($field_name)}
    {$field_name='pwd'}
{/if}
{if empty($translator_phrase)}
    {$translator_phrase='pwd'}
{/if}
{include file="asset:System/Partials/form_group.input_password.tpl" name=$field_name label={lang t="users|`$translator_phrase`"}}
{include file="asset:System/Partials/form_group.input_password.tpl" name="`$field_name`_repeat" label={lang t="users|`$translator_phrase`_repeat"}}
