<script type="text/javascript">
    $(document).ready(function () {
        $('#redirect-message').delay(4000).animate({ height: 'hide' });
    });
</script>
<div id="redirect-message" class="alert alert-{if $redirect.success === true}success{else}danger{/if} text-center">
    <strong>{$redirect.text}</strong>
</div>