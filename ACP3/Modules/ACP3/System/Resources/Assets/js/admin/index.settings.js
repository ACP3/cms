((document) => {
  const subtitleHomepageHomeContainer = document.getElementById("site-subtitle-homepage-home-container");
  const maintenanceModeMessageContainer = document.getElementById("maintenance-message-container");
  const mailerSmtp1Container = document.getElementById("mailer-smtp-1");
  const mailerSmtp2Container = document.getElementById("mailer-smtp-2");

  document.querySelectorAll('[name="site_subtitle_mode"]').forEach((elem) => {
    elem.addEventListener("change", () => {
      subtitleHomepageHomeContainer.classList.toggle("d-none", Number(elem.value) === 3);
    });

    if (elem.checked) {
      elem.dispatchEvent(new InputEvent("change"));
    }
  });

  document.querySelectorAll('[name="maintenance_mode"]').forEach((elem) => {
    elem.addEventListener("change", () => {
      maintenanceModeMessageContainer.classList.toggle("d-none", Number(elem.value) === 0);
    });

    if (elem.checked) {
      elem.dispatchEvent(new InputEvent("change"));
    }
  });

  document.querySelectorAll('[name="mailer_smtp_auth"]').forEach((elem) => {
    elem.addEventListener("change", () => {
      mailerSmtp2Container.classList.toggle("d-none", Number(elem.value) === 0);
    });

    if (elem.checked) {
      elem.dispatchEvent(new InputEvent("change"));
    }
  });

  const mailerTypeFormField = document.getElementById("mailer-type");
  mailerTypeFormField.addEventListener("change", () => {
    if (mailerTypeFormField.value === "smtp") {
      mailerSmtp1Container.classList.remove("d-none");
      document.querySelector('[name="mailer_smtp_auth"]:checked').dispatchEvent(new InputEvent("change"));
    } else {
      mailerSmtp1Container.classList.add("d-none");
      mailerSmtp2Container.classList.add("d-none");
    }
  });
  mailerTypeFormField.dispatchEvent(new InputEvent("change"));
})(document);
