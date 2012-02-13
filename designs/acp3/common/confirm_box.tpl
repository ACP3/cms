<div class="combo-box">
	<div class="header">{$confirm.text}</div>
	<div class="content">
{if isset($confirm.backward)}
		<a href="{$confirm.forward}" class="form">{lang t="common|yes"}</a>
		<a href="{$confirm.backward}" class="form">{lang t="common|no"}</a>
{elseif $confirm.overlay == 1}
		<a href="javascript:window.parent.$.fancybox.close()" class="form">{lang t="common|close_window"}</a>
{else}
		<a href="{$confirm.forward}" class="form">{lang t="common|forward"}</a>
{/if}
	</div>
</div>