<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $(document).on('acp3.ajaxFrom.submit.before', function () {
            if (typeof CKEDITOR !== "undefined") {
                for (var instance in CKEDITOR.instances) {
                    CKEDITOR.instances[instance].updateElement();
                }
            }
        });
    });
</script>
