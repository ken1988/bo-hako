(function () {
  'use strict';

  function initOwnerTabs() {
    var page = document.getElementById('owner-page');
    var navigation = document.getElementById('owner-section-nav');
    if (!page || !navigation) return;

    navigation.setAttribute('role', 'tablist');
    var tabs = Array.prototype.slice.call(navigation.querySelectorAll('a'));
    var groups = {
      daily: Array.prototype.slice.call(page.querySelectorAll('.owner-section-daily')),
      national: Array.prototype.slice.call(page.querySelectorAll('.owner-section-national')),
      links: Array.prototype.slice.call(page.querySelectorAll('.owner-section-links')),
      display: Array.prototype.slice.call(page.querySelectorAll('.owner-section-display'))
    };
    var tabGroups = ['daily', 'national', 'links', 'display'];

    function activate(group, updateHistory) {
      if (!groups[group]) group = 'daily';

      tabGroups.forEach(function (name, index) {
        var selected = name === group;
        var tabId = 'owner-tab-' + name;
        var controlledIds = groups[name].map(function (section) { return section.id; }).join(' ');
        tabs[index].id = tabId;
        tabs[index].setAttribute('role', 'tab');
        tabs[index].setAttribute('aria-controls', controlledIds);
        tabs[index].setAttribute('aria-selected', selected ? 'true' : 'false');
        tabs[index].setAttribute('tabindex', selected ? '0' : '-1');
        groups[name].forEach(function (section) {
          section.setAttribute('role', 'tabpanel');
          section.setAttribute('aria-labelledby', tabId);
          section.hidden = !selected;
        });
      });

      if (updateHistory && window.history && window.history.replaceState) {
        var selectedUrl = new URL(tabs[tabGroups.indexOf(group)].getAttribute('href'), window.location.href);
        var currentPageWithHash = window.location.pathname + window.location.search + selectedUrl.hash;
        window.history.replaceState(null, '', currentPageWithHash);
      }
    }

    tabs.forEach(function (tab, index) {
      tab.addEventListener('click', function (event) {
        event.preventDefault();
        activate(tabGroups[index], true);
      });
      tab.addEventListener('keydown', function (event) {
        if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') return;
        event.preventDefault();
        var direction = event.key === 'ArrowRight' ? 1 : -1;
        var nextIndex = (index + direction + tabs.length) % tabs.length;
        tabs[nextIndex].focus();
        activate(tabGroups[nextIndex], true);
      });
    });

    var hash = window.location.hash;
    var initialGroup = hash.indexOf('national') !== -1 ? 'national' :
      hash.indexOf('related') !== -1 ? 'links' :
      hash.indexOf('display') !== -1 ? 'display' : 'daily';
    activate(initialGroup, false);
    page.classList.add('owner-tabs-enabled');
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initOwnerTabs);
  } else {
    initOwnerTabs();
  }
})();
