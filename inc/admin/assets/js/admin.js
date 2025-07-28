jQuery(document).ready(function ($) {
  function openMediaUploader(inputId, previewId) {
    const customUploader = wp.media({
      title: "Choisir une image",
      button: { text: "Utiliser cette image" },
      multiple: false,
    });

    customUploader.on("select", function () {
      const attachment = customUploader
        .state()
        .get("selection")
        .first()
        .toJSON();
      $("#" + inputId).val(attachment.url);
      $("#" + previewId).html(
        '<img src="' + attachment.url + '" style="max-height: 100px;">'
      );
    });

    customUploader.open();
  }

  $(".vc-upload-logo").on("click", function () {
    openMediaUploader("vc_app_logo", "vc_app_logo_preview");
  });

  $(".vc-upload-banner").on("click", function () {
    openMediaUploader("vc_app_banner", "vc_app_banner_preview");
  });

  //pour features

  // Gestion de l’upload image (inchangé)
  $(document).on("click", ".vc-upload-feature-image", function (e) {
    e.preventDefault();
    let $button = $(this);
    const customUploader = wp.media({
      title: "Choisir une image de feature",
      button: { text: "Utiliser cette image" },
      multiple: false,
    });

    customUploader.on("select", function () {
      const attachment = customUploader
        .state()
        .get("selection")
        .first()
        .toJSON();
      $button.siblings(".vc-feature-image-url").val(attachment.url);
      $button
        .siblings(".vc-feature-image-preview")
        .html('<img src="' + attachment.url + '" style="max-height: 100px;">');
    });

    customUploader.open();
  });

  // Supprimer une feature
  $(document).on("click", ".remove-feature", function (e) {
    e.preventDefault();
    if ($(".vc-feature-block").length > 1) {
      $(this).closest(".vc-feature-block").remove();
    } else {
      alert("Il doit y avoir au moins une feature.");
    }
  });

  //pour la catégorie
  $("#add_new_category").on("click", function (e) {
    e.preventDefault();
    const catName = $("#new_category_name").val();

    if (catName.trim() === "") return;

    $.post(
      ajaxurl,
      {
        action: "vc_add_custom_category",
        name: catName,
      },
      function (response) {
        if (response.success) {
          const newCat = $("<option>")
            .val(response.data.term_id)
            .text(response.data.name);
          $("#vc_app_category").append(newCat);
          $("#vc_app_category").val(response.data.term_id);
          $("#new_category_name").val("");
          $("#cat-msg")
            .text("Catégorie ajoutée !")
            .css("color", "green")
            .fadeIn()
            .delay(2000)
            .fadeOut();
        } else {
          $("#cat-msg")
            .text("Erreur : " + response.data.message)
            .css("color", "red")
            .fadeIn()
            .delay(3000)
            .fadeOut();
        }
      }
    );
  });

  //menu
  function switchContainer(targetId) {
    // Cacher tous les conteneurs
    $(".container-infos > div").hide();

    // Afficher uniquement le conteneur sélectionné
    $(targetId).show();

    // Réinitialiser l'état actif
    $(".container-menus .menu-info").removeClass("active");

    // Ajouter la classe active à la div contenant le lien
    $('.container-menus a[href="' + targetId + '"]')
      .closest(".menu-info")
      .addClass("active");
  }

  // Par défaut
  switchContainer("#container-details");

  // Au clic
  $(".container-menus a").on("click", function (e) {
    e.preventDefault();
    const targetId = $(this).attr("href");
    switchContainer(targetId);
  });

  const multiPlatformSelect = document.getElementById("vc_multi_platform");
  const multiPlatformUrlField = document.getElementById("multi-platform-url");
  function updatePlatformUrlVisibility() {
    if (multiPlatformSelect && multiPlatformUrlField) {
      multiPlatformUrlField.style.display =
        multiPlatformSelect.value === "yes" ? "block" : "none";
    }
  }
  updatePlatformUrlVisibility();
  if (multiPlatformSelect)
    multiPlatformSelect.addEventListener("change", updatePlatformUrlVisibility);
});

//pour pricing

document.addEventListener("DOMContentLoaded", function () {
  // ✅ Générateur d'ID unique
  const uid = () => Math.floor(Math.random() * 100000);

  // ✅ Initialiser les index pour éviter collisions
  let monthlyIndex = document.querySelectorAll(
    "#monthly-plans .plan-block"
  ).length;
  let yearlyIndex = document.querySelectorAll(
    "#yearly-plans .plan-block"
  ).length;

  // ✅ Ajouter un plan
  document.querySelectorAll(".add-plan-button").forEach((button) => {
    button.addEventListener("click", function () {
      const type = this.getAttribute("data-type"); // 'monthly' ou 'yearly'
      const templateId =
        type === "monthly"
          ? "vc-plan-template-monthly"
          : type === "yearly"
          ? "vc-plan-template-yearly"
          : "vc-plan-template";
      const containerId =
        type === "monthly"
          ? "monthly-plans"
          : type === "yearly"
          ? "yearly-plans"
          : null;

      const template = document.getElementById(templateId);
      const container = document.getElementById(containerId);
      if (!template || !container) return;

      let index;
      if (type === "monthly") {
        index = monthlyIndex++;
      } else if (type === "yearly") {
        index = yearlyIndex++;
      } else if (type === "general") {
        // Pour cas 1 → plan_0, plan_1, ...
        if (typeof window.generalPlanIndex === "undefined") {
          window.generalPlanIndex = 0;
        }
        index = "plan_" + window.generalPlanIndex++;
      }

      // ✅ Cloner le contenu et remplacer __index__
      const clone = template.content.cloneNode(true);
      clone.querySelectorAll("[name]").forEach((input) => {
        input.name = input.name.replace(/__index__/g, index);
      });

      container.appendChild(clone);
    });
  });

  // ✅ Supprimer un plan
  document.addEventListener("click", function (e) {
    if (e.target.classList.contains("remove-plan")) {
      e.preventDefault();
      const block = e.target.closest(".plan-block");
      if (block) block.remove();
    }
  });

  // ✅ Affichage conditionnel selon les sélections abonnement/multi
  const toggleSections = () => {
    const subSelect = document.getElementById("vc_subscription");
    const multiSelect = document.getElementById("vc_multi_plan");

    if (!subSelect || !multiSelect) return;

    const sub = subSelect.value;
    const multi = multiSelect.value;

    document
      .querySelectorAll(".pricing-section")
      .forEach((el) => (el.style.display = "none"));

    if (sub === "no" && multi === "no") {
      document
        .getElementById("pricing_simple_nosub")
        ?.style?.setProperty("display", "block");
    } else if (sub === "no" && multi === "yes") {
      document
        .getElementById("pricing_single_multi_nosub")
        ?.style?.setProperty("display", "block");
    } else if (sub === "yes" && multi === "no") {
      document
        .getElementById("pricing_simple_withsub")
        ?.style?.setProperty("display", "block");
    } else if (sub === "yes" && multi === "yes") {
      document
        .getElementById("pricing_multi_withsub")
        ?.style?.setProperty("display", "block");
    }
  };

  // ✅ Listener changements dropdown
  const subSelect = document.getElementById("vc_subscription");
  const multiSelect = document.getElementById("vc_multi_plan");

  if (subSelect) subSelect.addEventListener("change", toggleSections);
  if (multiSelect) multiSelect.addEventListener("change", toggleSections);

  // ✅ Initialiser la section visible
  toggleSections();

  let generalIndex = document.querySelectorAll(
    "#general-plans .plan-block"
  ).length;

  document.querySelectorAll(".add-plan-button").forEach((button) => {
    button.addEventListener("click", function () {
      const type = this.getAttribute("data-type"); // ici = 'general'
      const template = document.getElementById("vc-plan-template");
      const container = document.getElementById("general-plans");
      if (!template || !container) return;

      const id = "plan_" + generalIndex++;

      const clone = template.content.cloneNode(true);
      clone.querySelectorAll("[name]").forEach((input) => {
        input.name = input.name.replace(/__index__/g, id);
      });

      container.appendChild(clone);
    });
  });

  // Supprimer un plan
  document.addEventListener("click", function (e) {
    if (e.target.classList.contains("remove-plan")) {
      e.preventDefault();
      const block = e.target.closest(".plan-block");
      if (block) block.remove();
    }
  });


});

jQuery(document).ready(function ($) {
  let featureIndex = $("#vc_features_container .vc-feature-block").length;
  let faqIndex = $("#vc_faqs_container .vc-faq-block").length;

  $("#add-feature-button").on("click", function (e) {
    e.preventDefault();
    const template = $("#vc-feature-template")
      .html()
      .replace(/__index__/g, featureIndex);
    $("#vc_features_container").append(template);
    featureIndex++;
  });

  $("#vc_features_container").on("click", ".remove-feature", function (e) {
    e.preventDefault();
    $(this).closest(".vc-feature-block").remove();
  });

  $("#add-faq-button").on("click", function (e) {
    e.preventDefault();
    const template = $("#vc-faq-template")
      .html()
      .replace(/__index__/g, faqIndex);
    $("#vc_faqs_container").append(template);
    faqIndex++;
  });

  $("#vc_faqs_container").on("click", ".remove-faq", function (e) {
    e.preventDefault();
    $(this).closest(".vc-faq-block").remove();
  });
});


//pour reviews
jQuery(document).ready(function ($) {
  // Initialisation de l'index à partir du nombre d'avis déjà présents
  let reviewIndex = $("#vc_reviews_container .vc-review-block").length;

  // Ajout d'un avis
  $("#add-review-button").on("click", function (e) {
    e.preventDefault();
    const template = $("#vc-review-template")
      .html()
      .replace(/__index__/g, reviewIndex);
    $("#vc_reviews_container").append(template);
    reviewIndex++;
  });

  // Suppression d'un avis
  $("#vc_reviews_container").on("click", ".remove-review", function (e) {
    e.preventDefault();
    $(this).closest(".vc-review-block").remove();
  });
});