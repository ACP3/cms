(($, document) => {
  $('input[name="readmore"]')
    .on("click change", function () {
      document.getElementById("readmore-characters-wrapper").classList.toggle("hidden", Number(this.value) === 0);
    })
    .filter(":checked")
    .click();
})(jQuery, document);
