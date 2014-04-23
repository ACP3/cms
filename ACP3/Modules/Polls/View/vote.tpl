<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal ajax-form">
    <div class="form-group">
        <label for="answer-{$answers.0.id}" class="col-lg-2 control-label">{$question}</label>

        <div class="col-lg-10">
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
    <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
            <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
        </div>
    </div>
</form>
{include_js module="system" file="forms"}
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('.ajax-form').formSubmit('{lang t="system|loading_please_wait"}');
    });
</script>