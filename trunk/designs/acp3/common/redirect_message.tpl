<script type="text/javascript">
$(document).ready(function() {
	$('#redirect-message').delay(4000).animate({ height: 'hide' });
});
</script>
<div id="redirect-message" class="alert alert-{if $redirect.success === true}success{else}error{/if} align-center">
	<h4 class="alert-heading">{$redirect.text}</h4>
</div>