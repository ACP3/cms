jQuery(document).ready(($) => {
    $(':radio[name="enabled"]').on('click change', function () {
        $('#cookie-consent-container').toggle(Number(this.value) === 1);
    }).filter(':checked').triggerHandler('click');
});
