/**
 * The default theme's script. Bootstrap handles offcanvas, collapse, modals and toasts; only the
 * behaviour Bootstrap and htmx do not provide lives here. It targets the generic markup the default
 * theme and the components' default templates ship, and nothing application specific.
 */
(function () {
  'use strict';

  // ── Light / dark toggle ─────────────────────────────────────────────────
  document.addEventListener('click', function (e) {
    if (!e.target.closest('[data-theme-toggle]')) {
      return;
    }
    var next = document.documentElement.dataset.bsTheme === 'dark' ? 'light' : 'dark';
    document.documentElement.dataset.bsTheme = next;
    try {
      localStorage.setItem('webware-theme', next);
    } catch (err) {}
  });

  // ── Server-rendered toasts ──────────────────────────────────────────────
  htmx.onLoad(function (root) {
    root.querySelectorAll('.toast').forEach(function (toast) {
      bootstrap.Toast.getOrCreateInstance(toast).show();
    });
  });

  // ── Dock ────────────────────────────────────────────────────────────────
  // The dock lives inside <main>, so hide it before a boosted navigation swaps the body.
  document.addEventListener('htmx:beforeRequest', function () {
    var dock = document.getElementById('appDock');
    var instance = dock ? bootstrap.Offcanvas.getInstance(dock) : null;
    if (instance) {
      instance.hide();
    }
  });

  // ── Shared modal ────────────────────────────────────────────────────────
  // Handlers send HX-Trigger closeModal after a successful save. The modal is hidden through
  // Bootstrap so it removes its own backdrop; the brute-force clean-up covers a swap that raced it.
  function cleanModalBackdrop() {
    document.querySelectorAll('.modal-backdrop').forEach(function (el) { el.remove(); });
    document.body.classList.remove('modal-open');
    document.body.style.removeProperty('overflow');
    document.body.style.removeProperty('padding-right');
  }

  document.addEventListener('closeModal', function () {
    var open = document.querySelector('.modal.show');
    var instance = open ? bootstrap.Modal.getInstance(open) : null;
    if (instance) {
      open.addEventListener('hidden.bs.modal', cleanModalBackdrop, { once: true });
      instance.hide();
      return;
    }
    cleanModalBackdrop();
  });

  document.addEventListener('htmx:afterSwap', cleanModalBackdrop);

  // Empty the shared dialog once it has closed so stale content never lingers between uses.
  document.addEventListener('hidden.bs.modal', function (e) {
    if (e.target.id === 'sharedModal') {
      document.getElementById('sharedModalDialog').innerHTML = '';
    }
  });
})();

// Keep the Tracy bar out of the body while htmx replaces it during a boosted request.
(function () {
  var tracyEl = null;

  document.addEventListener('htmx:beforeSwap', function () {
    tracyEl = document.getElementById('tracy-debug');
    if (tracyEl) {
      tracyEl.remove();
    }
  });

  // afterSwap, not afterSettle, so the bar is back before Tracy's async script calls loadAjax().
  document.addEventListener('htmx:afterSwap', function () {
    if (tracyEl) {
      document.body.appendChild(tracyEl);
      tracyEl = null;
    }
  });
})();

// Let htmx swap 4xx and 5xx responses, such as a 422 carrying validation errors.
document.addEventListener('htmx:beforeSwap', function (evt) {
  if (evt.detail.xhr.status >= 400) {
    evt.detail.shouldSwap = true;
    evt.detail.isError = false;
  }
});
