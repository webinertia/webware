/**
 * Toasts raised from the client. The layout carries the #systemMessage container and the
 * #toastTemplate this fills in; the server raises the same toasts with the systemMessage event.
 */
function systemMessage(level, msg) {
  const template = document.getElementById('toastTemplate');
  const clone = template.content.firstElementChild.cloneNode(true);
  clone.classList.add(`text-bg-${level ?? 'info'}`);
  clone.querySelector('.toast-message').textContent = msg;
  const container = document.getElementById('systemMessage');
  const toast = new bootstrap.Toast(clone, { autohide: true, delay: 4000 });
  clone.addEventListener('hidden.bs.toast', () => clone.remove());
  container.appendChild(clone);
  toast.show();
}

// The server-triggered systemMessage event.
htmx.on('systemMessage', (evt) => systemMessage(evt.detail.level, evt.detail.message));

// A failed request shows the response text.
htmx.on('htmx:responseError', (evt) => systemMessage('danger', evt.detail.xhr.responseText));
