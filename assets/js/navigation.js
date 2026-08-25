/* Global navigation behavior shared by every public template. */
(function () {
  'use strict';

  function initNavigation() {
    var header = document.querySelector('.site-header');
    var button = document.querySelector('.nav-toggle');
    var menu = document.getElementById('site-menu');
    var backgroundStates = [];

    function updateHeaderOffset() {
      if (!header) return;
      var height = header.offsetHeight || 0;
      document.documentElement.style.setProperty('--header-h', height + 'px');
      document.documentElement.style.setProperty('--tm-header-height', height + 'px');
    }

    function updateScrolledState() {
      if (!header) return;
      header.classList.toggle('scrolled', window.scrollY > 10);
    }

    function updateAdminBarOffset() {
      var offset = 0;
      if (document.body.classList.contains('admin-bar')) {
        var adminBar = document.getElementById('wpadminbar');
        var adminBarHeight = adminBar ? adminBar.offsetHeight : (window.innerWidth <= 782 ? 46 : 32);
        offset = window.innerWidth <= 600
          ? Math.max(0, adminBarHeight - window.scrollY)
          : adminBarHeight;
      }
      document.body.style.setProperty('--tm-admin-bar-offset', offset + 'px');
    }

    if (!header || !button || !menu) {
      updateHeaderOffset();
      updateAdminBarOffset();
      return;
    }

    function isOpen() {
      return button.getAttribute('aria-expanded') === 'true';
    }

    function setBackgroundInert(makeInert) {
      if (makeInert) {
        backgroundStates = [];
        Array.prototype.forEach.call(document.body.children, function (element) {
          if (element === header || element.contains(header) || /^(SCRIPT|STYLE|LINK)$/.test(element.tagName)) return;
          var supportsInert = 'inert' in element;
          backgroundStates.push({
            element: element,
            supportsInert: supportsInert,
            previousInert: supportsInert ? element.inert : false,
            previousAriaHidden: element.getAttribute('aria-hidden')
          });
          if (supportsInert) element.inert = true;
          else element.setAttribute('aria-hidden', 'true');
        });
        return;
      }

      backgroundStates.forEach(function (state) {
        if (state.supportsInert) state.element.inert = state.previousInert;
        if (state.previousAriaHidden === null) state.element.removeAttribute('aria-hidden');
        else state.element.setAttribute('aria-hidden', state.previousAriaHidden);
      });
      backgroundStates = [];
    }

    function getMenuFocusable() {
      var links = Array.prototype.slice.call(menu.querySelectorAll(
        'a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])'
      ));
      return [button].concat(links).filter(function (element, index, items) {
        return items.indexOf(element) === index && element.getAttribute('aria-hidden') !== 'true';
      });
    }

    function setOpen(open, restoreFocus) {
      header.classList.toggle('open', open);
      menu.classList.toggle('open', open);
      button.classList.toggle('active', open);
      document.body.classList.toggle('tm-menu-open', open);
      button.setAttribute('aria-expanded', open ? 'true' : 'false');
      button.setAttribute('aria-label', open ? 'Cerrar menú principal' : 'Abrir menú principal');
      setBackgroundInert(open);
      updateHeaderOffset();

      if (open) {
        window.requestAnimationFrame(function () {
          if (!isOpen()) return;
          var focusable = getMenuFocusable();
          (focusable[1] || focusable[0]).focus();
        });
      } else if (restoreFocus) {
        button.focus();
      }
    }

    button.addEventListener('click', function () {
      setOpen(!isOpen(), false);
    });

    menu.addEventListener('click', function (event) {
      var link = event.target.closest ? event.target.closest('a') : null;
      if (link && isOpen()) setOpen(false, true);
    });

    document.addEventListener('click', function (event) {
      if (!isOpen() || header.contains(event.target)) return;
      setOpen(false, true);
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && isOpen()) {
        event.preventDefault();
        setOpen(false, true);
      } else if (event.key === 'Tab' && isOpen()) {
        var focusable = getMenuFocusable();
        if (!focusable.length) return;
        var first = focusable[0];
        var last = focusable[focusable.length - 1];
        if (event.shiftKey && (document.activeElement === first || !header.contains(document.activeElement))) {
          event.preventDefault();
          last.focus();
        } else if (!event.shiftKey && (document.activeElement === last || !header.contains(document.activeElement))) {
          event.preventDefault();
          first.focus();
        }
      }
    });

    window.addEventListener('resize', function () {
      if (window.innerWidth > 1024 && isOpen()) setOpen(false, false);
      updateHeaderOffset();
      updateAdminBarOffset();
    });
    window.addEventListener('scroll', function () {
      updateScrolledState();
      updateAdminBarOffset();
    }, { passive: true });
    window.addEventListener('load', updateHeaderOffset);

    if (typeof window.ResizeObserver === 'function') {
      new window.ResizeObserver(updateHeaderOffset).observe(header);
    }

    updateHeaderOffset();
    updateScrolledState();
    updateAdminBarOffset();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initNavigation);
  } else {
    initNavigation();
  }
})();
