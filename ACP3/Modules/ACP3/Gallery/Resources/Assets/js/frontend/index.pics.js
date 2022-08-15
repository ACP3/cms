(() => {
  /* global PhotoSwipeLightbox, PhotoSwipe */
  const lightbox = new PhotoSwipeLightbox({
    gallery: ".gallery-pictures",
    children: ".gallery-picture-thumb",
    pswpModule: PhotoSwipe,
  });
  lightbox.init();
})();
