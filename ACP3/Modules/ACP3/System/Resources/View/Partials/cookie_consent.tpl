<div id="cookie-consent" class="cookie-consent clearfix hidden">
    <div class="container">
        <button id="accept-cookies" class="cookie-consent__button btn btn-sm btn-success pull-right">
            {lang t="system|accept"}
        </button>
        {$cookie_consent_text}
    </div>
</div>
{javascripts}
    {include_js module="system" file="partials/cookie-consent" depends='js-cookie'}
{/javascripts}
