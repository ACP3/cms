((window, document) => {
  window.addEventListener("hashchange", function () {
    const hash = window.location.hash,
      linkElem = document.querySelector('a[href="' + hash + '"]'),
      targetElem = document.querySelector(hash);

    if (linkElem?.length) {
      linkElem.dispatchEvent(new MouseEvent("click"));
    } else if (targetElem?.length) {
      targetElem.dispatchEvent(new MouseEvent("click"));
    }
  });

  if (window.location.hash) {
    window.dispatchEvent(new CustomEvent("hashchange"));
  }
})(window, document);
