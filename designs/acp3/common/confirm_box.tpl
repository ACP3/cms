<div class="modal">
	<div class="modal-header align-center"><h4>{$confirm.text}<h4></div>
	<div class="modal-footer align-center">
{if isset($confirm.backward)}
		<a href="{$confirm.forward}" class="btn">{lang t="common|yes"}</a>
		<a href="{$confirm.backward}" class="btn">{lang t="common|no"}</a>
{elseif $confirm.overlay == 1}
		<a href="javascript:window.parent.$.fancybox.close()" class="btn">{lang t="common|close_window"}</a>
{else}
		<a href="{$confirm.forward}" class="btn">{lang t="common|forward"}</a>
{/if}
	</div>
</div>