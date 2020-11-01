/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

class CookieConsentManager {
    #type;
    #cookieConsentCookieName;
    #cookieExpiry = 365;
    #defaultCookieTypes = {
        mandatory: true,
        advertising: false,
        analytics: false,
        comfort: false,
        security: false,
    };

    constructor(type, cookieConsentCookieName) {
        this.#type = type;
        this.#cookieConsentCookieName = cookieConsentCookieName;
    }

    initialise() {
        setTimeout(() => {
            // If the cookie consent type has been set to "informational" or "opt-out",
            // we can already set the cookie-consent-cookie.
            if (this.#type !== 'opt-in') {
                this.acceptAll();
            } else if (this.hasConsented()) {
                this.#triggerEvent(JSON.parse(this.#getCookie()));
            } else {
                this.#triggerEvent(this.#defaultCookieTypes);
            }
        });
    }

    acceptAll() {
        this.accept({ mandatory: true, advertising: true, analytics: true, comfort: true, security: true });
    }

    acceptOnlyNecessary() {
        this.accept({ mandatory: true, advertising: false, analytics: false, comfort: false, security: false });
    }

    accept(cookieTypes) {
        this.#setCookie(cookieTypes);
        this.#triggerEvent(cookieTypes);
    }

    hasConsented() {
        let consentCookieValue;

        try {
            consentCookieValue = JSON.parse(this.#getCookie()) || {};
        } catch (e) {
            consentCookieValue = {};
        }
        const defaultCookieTypes = Object.keys(this.#defaultCookieTypes).sort();
        const consentedCookieTypes = Object.keys(consentCookieValue || {}).sort();

        return JSON.stringify(defaultCookieTypes) === JSON.stringify(consentedCookieTypes);
    }

    #getCookie() {
        return Cookies.get(this.#cookieConsentCookieName);
    }

    #setCookie(cookieTypes) {
        Cookies.set(this.#cookieConsentCookieName, Object.assign({}, this.#defaultCookieTypes, cookieTypes), {expires: this.#cookieExpiry, SameSite: 'Strict'});
    }

    #triggerEvent(cookieTypes) {
        const event = new CustomEvent('acp3.cookieConsent', {
            detail: Object.assign({}, this.#defaultCookieTypes, cookieTypes),
        });

        document.dispatchEvent(event);
    }
}

jQuery(document).ready(($) => {
    const $cookieConsent = $('#cookie-consent');

    const cookieConsentManager = new CookieConsentManager(
        $cookieConsent.data('cookieConsentType'),
        'ACP3_COOKIE_CONSENT',
    );

    if (!cookieConsentManager.hasConsented()) {
        $cookieConsent.removeClass('hidden');
    }

    cookieConsentManager.initialise();

    $cookieConsent.find('button').click(function () {
        if ($(this).data('cookieConsentAcceptType') === 'acceptAll') {
            cookieConsentManager.acceptAll();
        } else {
            cookieConsentManager.acceptOnlyNecessary();
        }

        $cookieConsent.fadeOut();
    });

    document.addEventListener('acp3.cookieConsent', (e) => {
        console.log(e);
    });
});
