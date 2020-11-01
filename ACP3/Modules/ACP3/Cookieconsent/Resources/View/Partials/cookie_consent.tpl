<div id="cookie-consent" class="cookie-consent clearfix hidden" data-cookie-consent-type="{$cookie_consent_type}">
    <div class="container">
        {if $cookie_consent_type === 'informational'}
            <button class="cookie-consent__button btn btn-sm btn-success pull-right" data-cookie-consent-accept-type="acceptAll">
                {lang t="cookieconsent|accept"}
            </button>
        {else}
            <button class="cookie-consent__button btn btn-sm btn-success pull-right" data-cookie-consent-accept-type="acceptAll">
                {lang t="cookieconsent|accept_all"}
            </button>
            <button class="cookie-consent__button btn btn-sm btn-default pull-right" data-cookie-consent-accept-type="acceptNecessary">
                {lang t="cookieconsent|accept_technically_necessary"}
            </button>
        {/if}
        <div class="cookie-consent__body">
            {$cookie_consent_text|rewrite_uri}
        </div>
    </div>
</div>
{javascripts}
    {include_js module="cookieconsent" file="partials/cookie-consent" depends='js-cookie'}
{/javascripts}
