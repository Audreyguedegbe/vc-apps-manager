document.addEventListener('DOMContentLoaded', function () {
  const monthlyBtn = document.getElementById('monthly-btn');
  const yearlyBtn = document.getElementById('yearly-btn');
  const monthlyContent = document.getElementById('monthly-content');
  const yearlyContent = document.getElementById('yearly-content');

  const monthlyRadios = monthlyContent.querySelectorAll('input[type="radio"]');
  const yearlyRadios = yearlyContent.querySelectorAll('input[type="radio"]');

  const syncLabels = (index) => {
    // Désactive tous les labels Monthly
    monthlyContent.querySelectorAll('.radio-label').forEach((el, i) => {
      el.classList.toggle('active', i === index);
      el.querySelector('.radio-content').style.display = i === index ? 'block' : 'none';
      el.querySelector('input[type="radio"]').checked = i === index;
    });

    // Désactive tous les labels Yearly
    yearlyContent.querySelectorAll('.radio-label').forEach((el, i) => {
      el.classList.toggle('active', i === index);
      el.querySelector('.radio-content').style.display = i === index ? 'block' : 'none';
      el.querySelector('input[type="radio"]').checked = i === index;
    });
  };

  // Toggle buttons
  monthlyBtn.addEventListener('click', function () {
    monthlyBtn.classList.add('active');
    yearlyBtn.classList.remove('active');
    monthlyContent.classList.add('active');
    yearlyContent.classList.remove('active');
  });

  yearlyBtn.addEventListener('click', function () {
    yearlyBtn.classList.add('active');
    monthlyBtn.classList.remove('active');
    yearlyContent.classList.add('active');
    monthlyContent.classList.remove('active');
  });

  // Synchronisation entre les radios
  monthlyRadios.forEach((radio, index) => {
    radio.addEventListener('change', () => syncLabels(index));
  });

  yearlyRadios.forEach((radio, index) => {
    radio.addEventListener('change', () => syncLabels(index));
  });


  //pour le prix
  const priceDisplay = document.getElementById('vc-current-plan-price');

    function updateActivePrice() {
  const activeContent = document.querySelector('.content.active');
  const activeLabel = activeContent.querySelector('.radio-label.active');
  const price = activeLabel?.querySelector('.price')?.textContent || '--';
  const title = activeLabel?.querySelector('.title-plan')?.textContent || '';
  const type = activeContent.id === 'monthly-content' ? 'Monthly' : 'Yearly';
  const urlButton = activeLabel?.querySelector('.radio-content a');
  const url = urlButton ? urlButton.getAttribute('href') : '#';

  // Met à jour les éléments dans la pricing bar
  document.getElementById('vc-current-plan-title').textContent = title;
  document.querySelectorAll('.vc-current-plan-price').forEach(el => {
  el.textContent = price;
});
  document.getElementById('vc-current-plan-type').textContent = type;
  document.getElementById('vc-current-plan-url').setAttribute('href', url);

  // Affiche la barre uniquement si un titre est présent
  const bar = document.getElementById('vc-current-plan-bar');
  bar.style.display = title ? 'block' : 'none';
}


    // Appel initial
    updateActivePrice();

    // Mise à jour quand on clique sur un toggle (Monthly / Yearly)
    document.getElementById('monthly-btn').addEventListener('click', function () {
        document.getElementById('monthly-content').classList.add('active');
        document.getElementById('yearly-content').classList.remove('active');
        updateActivePrice();
    });

    document.getElementById('yearly-btn').addEventListener('click', function () {
        document.getElementById('yearly-content').classList.add('active');
        document.getElementById('monthly-content').classList.remove('active');
        updateActivePrice();
    });

    // Mise à jour quand on clique sur un plan
    document.querySelectorAll('.radio-label').forEach(label => {
        label.addEventListener('click', function () {
            // Supprime tous les active du groupe courant
            const group = label.closest('.content');
            group.querySelectorAll('.radio-label').forEach(l => l.classList.remove('active'));
            group.querySelectorAll('.radio-content').forEach(c => c.style.display = 'none');

            // Active celui cliqué
            label.classList.add('active');
            label.querySelector('.radio-content').style.display = 'block';

            updateActivePrice();
        });
    });

  // Activation initiale (par défaut le premier)
  syncLabels(0);


  //pour faq

  const titles = document.querySelectorAll('.vc-faq-title');

  titles.forEach(function (title) {
    title.addEventListener('click', function () {
      const item = title.parentElement;
      const content = item.querySelector('.vc-faq-content');

      // Fermer tous les autres
      document.querySelectorAll('.vc-faq-content').forEach(function (el) {
          if (el !== content) el.style.display = 'none';
      });
      document.querySelectorAll('.vc-faq-title').forEach(t => t.classList.remove('active'));

      // Toggle actuel
      if (content.style.display === 'block') {
          content.style.display = 'none';
          title.classList.remove('active');
      } else {
          content.style.display = 'block';
          title.classList.add('active');
      }
    });
  });

  const faqTitles = document.querySelectorAll('.vc-faq-title');

  faqTitles.forEach(function (faqTitle) {
    faqTitle.addEventListener('click', function () {
      const content = faqTitle.nextElementSibling;

      // Fermer les autres
      document.querySelectorAll('.vc-faq-content').forEach(function (el) {
          if (el !== content) {
              el.style.maxHeight = null;
              el.previousElementSibling.classList.remove('active');
          }
      });

      if (content.style.maxHeight) {
          // Fermer l'actuel
          content.style.maxHeight = null;
          faqTitle.classList.remove('active');
      } else {
          // Ouvrir l'actuel
          content.style.maxHeight = content.scrollHeight + "px";
          faqTitle.classList.add('active');
      }
    });
  });

  //reviews
  document.querySelectorAll('.vc-stars .star').forEach(function (star) {
    star.addEventListener('click', function () {
        const value = this.dataset.value;
        document.getElementById('rating').value = value;

        document.querySelectorAll('.vc-stars .star').forEach(function (s) {
            s.style.color = (s.dataset.value <= value) ? '#ffc107' : '#ccc';
        });
    });
});

});

window.addEventListener('scroll', function () {
  const bar = document.getElementById('bar-content');
  const placeholder = document.getElementById('bar-placeholder');
  const features = document.getElementById('scrollable-content');

  const sectionTop = features.getBoundingClientRect().top;
  const sectionBottom = features.getBoundingClientRect().bottom;

  const barHeight = bar.offsetHeight;

  if (sectionTop <= 0 && sectionBottom > barHeight) {
    if (!bar.classList.contains('sticky')) {
      bar.classList.add('sticky');
      placeholder.style.height = barHeight + 'px'; // préserve l'espace
    }
  } else {
    if (bar.classList.contains('sticky')) {
      bar.classList.remove('sticky');
      placeholder.style.height = '0px'; // retire l'espace
    }
  }
});

//pour les reviews
document.addEventListener('DOMContentLoaded', function () {
    
});



