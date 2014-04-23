<div class="icons">
    {foreach $emoticons as $key => $value}
        <a href="#" title="{$key}">{$value}</a>
    {/foreach}
</div>
{include_js module="emoticons" file="list"}
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $('.icons a').insertEmoticon('#{$emoticons_field_id}');
    });
</script>