((document) => {
    const notificationToggle = document.querySelector('select[name="notify"]');

    notificationToggle.addEventListener('change', function (event) {
        if (event.detail?.init === true) {
            event.preventDefault();
        }

        document.getElementById('guestbook-entry-notification-wrapper').classList.toggle('hidden', Number(this.value) === 0);
    });

    notificationToggle.dispatchEvent(new CustomEvent('change', {detail: {init: true}}));
})(document);
