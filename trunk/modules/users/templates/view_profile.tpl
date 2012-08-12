<dl class="dl-horizontal">
	<dt>{lang t="users|nickname"}</dt>
	<dd>{$user.nickname}</dd>
{if $user.realname != '' && $user.realname_display == 1}
	<dt>{lang t="users|realname"}</dt>
	<dd>{$user.realname}</dd>
{/if}
{if $user.gender_display == 1}
	<dt>{lang t="users|gender"}</dt>
	<dd>{$user.gender}</dd>
{/if}
{if $user.birthday != '' && $user.birthday_display == 1}
	<dt>{lang t="users|birthday"}</dt>
	<dd>{$user.birthday}</dd>
{/if}
{if $user.mail_display == 1}
	<dt>{lang t="common|email"}</dt>
	<dd><a href="mailto:{$user.mail}" title="{lang t="users|send_email"}">{$user.mail}</a></dd>
{/if}
{if !empty($user.icq) && $user.icq_display == 1}
	<dt>{lang t="users|icq"}</dt>
	<dd>
		<a href="http://www.icq.com/{$user.icq}" onclick="window.open(this.href); return false">
			<img src="http://web.icq.com/whitepages/online?icq={$user.icq}&amp;img=27" alt="">
			{$user.icq}
		</a>
	</dd>
{/if}
{if $user.msn != '' && $user.msn_display == 1}
	<dt>{lang t="users|msn"}</dt>
	<dd><a href="http://members.msn.com/{$user.msn}" onclick="window.open(this.href); return false">{$user.msn}</a></dd>
{/if}
{if $user.skype != '' && $user.skype_display == 1}
	<dt>{lang t="users|skype"}</dt>
	<dd>
		<a href="skype:{$user.skype}?userinfo" onclick="window.open(this.href); return false">
			<img src="http://mystatus.skype.com/smallicon/{$user.skype}" alt="">
			{$user.skype}
		</a>
	</dd>
{/if}
{if $user.website != '' && $user.website_display == 1}
	<dt>{lang t="common|website"}</dt>
	<dd><a href="{$user.website}" onclick="window.open(this.href); return false" title="{lang t="users|visit_website"}">{$user.website}</a></dd>
{/if}
</dl>