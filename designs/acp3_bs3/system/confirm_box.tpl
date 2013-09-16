<div class="modal static">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header text-center">
				<h4 class="modal-title">{$confirm.text}</h4>
			</div>
			<div class="modal-footer text-center">
				{if isset($confirm.backward)}
					<a href="{$confirm.forward}" class="btn btn-default">{lang t="system|yes"}</a>
					<a href="{$confirm.backward}" class="btn btn-primary">{lang t="system|no"}</a>
				{elseif $confirm.overlay == 1}
					<a href="javascript:window.parent.$.fancybox.close();window.parent.location.reload();" class="btn btn-primary">{lang t="system|close_window"}</a>
				{else}
					<a href="{$confirm.forward}" class="btn btn-primary">{lang t="system|forward"}</a>
				{/if}
			</div>
		</div>
	</div>
</div>