(function () {
  'use strict';

  function initializeGlobalMenu() {
    var toggle = document.querySelector('.global-menu-toggle');
    var menu = document.getElementById('global-menu');

    if (!toggle || !menu) {
      return;
    }

    function closeMenu(returnFocus) {
      toggle.setAttribute('aria-expanded', 'false');
      menu.classList.remove('is-open');

      if (returnFocus) {
        toggle.focus();
      }
    }

    toggle.addEventListener('click', function () {
      var shouldOpen = toggle.getAttribute('aria-expanded') !== 'true';
      toggle.setAttribute('aria-expanded', String(shouldOpen));
      menu.classList.toggle('is-open', shouldOpen);
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
        closeMenu(true);
      }
    });

    window.addEventListener('resize', function () {
      if (window.matchMedia('(min-width: 701px)').matches) {
        closeMenu(false);
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeGlobalMenu);
  } else {
    initializeGlobalMenu();
  }
}());
