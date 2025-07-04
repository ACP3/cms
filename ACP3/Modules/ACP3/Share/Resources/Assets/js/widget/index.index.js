(() => {
  const shareButtonElements = document.querySelectorAll(".share-button");

  shareButtonElements.forEach((shareButton) => {
    shareButton.addEventListener("click", async () => {
      try {
        await navigator.share({
          title: shareButton.dataset.shareTitle || document.title,
          url: shareButton.dataset.shareUrl || window.location.url,
        });
      } catch (err) {
        console.error(err);
      }
    });
  });
})();
