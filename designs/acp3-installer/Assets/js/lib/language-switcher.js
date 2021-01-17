/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

export default class LanguageSwitcher {
    #languageSwitcherFormName;
    #languageSwitcherForm;
    #mustConfirm = false;

    constructor(languageSwitcherFormName) {
        this.#languageSwitcherFormName = languageSwitcherFormName;
        this.#languageSwitcherForm = document.getElementById(languageSwitcherFormName);
    }

    init() {
        this.#hideSubmitButton();
        this.#bindFormValuesChangesListener();
        this.#bindLanguageChangeListener();
    }

    #hideSubmitButton() {
        this.#languageSwitcherForm.querySelector('.btn').classList.add('hidden');
    }

    #bindFormValuesChangesListener() {
        document.querySelectorAll('input, textarea, select').forEach((elem) => {
            elem.addEventListener('change', (event) => {
                if (event.target.form.id === 'languages') {
                    return;
                }

                this.#mustConfirm = true;
            });
        });
    }

    #bindLanguageChangeListener() {
        document.getElementById('lang').addEventListener('change', (event) => {
            let canSubmitForm = true;

            if (this.#mustConfirm === true) {
                canSubmitForm = confirm(event.target.dataset.changeLanguageWarning);
            }

            if (canSubmitForm === true) {
                this.#languageSwitcherForm.submit();
            }
        });
    }
}
