{if $error_box.non_integer_keys === true}
    <script type="text/javascript">
        $(document).ready(function () {
            $('#error-box ul li').each(function () {
                var errorClass = $(this).data('error');
                if (errorClass.length > 0) {
                    $('#' + errorClass).parents('div.form-group').addClass('has-error');
                }
            });
            if ($('.tabbable').length > 0) {
                var tab_id = $('.tabbable .form-group.has-error:first').parents('.tab-pane').prop('id');
                $('.tabbable .nav-tabs a[href="#' + tab_id + '"]').tab('show');
            }
        });
    </script>
{/if}
<div id="error-box" class="alert alert-warning alert-block">
    <h4>{lang t="system|failure"}</h4>
    <ul>
        {foreach $error_box.errors as $key => $value}
            <li{if Validate::isNumber($key) === false} data-error="{$key}"{/if}>{$value}</li>
        {/foreach}
    </ul>
</div>