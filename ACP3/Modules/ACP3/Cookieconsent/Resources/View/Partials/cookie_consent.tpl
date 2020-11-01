<div id="cookie-consent" class="cookie-consent clearfix hidden">
    <div class="container">
        <button id="accept-cookies" class="cookie-consent__button btn btn-sm btn-success pull-right">
            {lang t="cookieconsent|accept"}
        </button>
        <div class="cookie-consent__body">
            {$cookie_consent_text|rewrite_uri}
        </div>
    </div>
</div>
{javascripts}
    {include_js module="cookieconsent" file="partials/cookie-consent" depends='js-cookie'}
{/javascripts}
