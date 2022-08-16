(() => {
  /* global PhotoSwipeLightbox, PhotoSwipe */
  const lightbox = new PhotoSwipeLightbox({
    gallery: ".gallery-pictures",
    children: ".gallery-picture-thumb",
    pswpModule: PhotoSwipe,
  });
  lightbox.on("uiRegister", () => {
    lightbox.pswp.ui.registerElement({
      name: "caption",
      order: 9,
      isButton: false,
      appendTo: "root",
      html: "",
      onInit: (el) => {
        lightbox.pswp.on("change", () => {
          const currSlideElement = lightbox.pswp.currSlide.data.element;
          const captionHTML = currSlideElement ? currSlideElement.querySelector("img").getAttribute("alt") : "";

          const captionInner = document.createElement("div");

          if (captionHTML) {
            captionInner.classList.add("pswp__caption-inner");
            captionInner.innerHTML = captionHTML || "";
          }

          el.replaceChildren(captionInner);
        });
      },
    });
  });
  lightbox.init();
})();
