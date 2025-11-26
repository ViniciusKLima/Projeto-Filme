// Toggle password visibility for inputs that have a sibling button `.toggle-password`
document.addEventListener("click", function (e) {
  const btn = e.target.closest(".toggle-password");
  if (!btn) return;
  const container = btn.parentElement;
  if (!container) return;
  const input = container.querySelector(
    'input[type="password"], input[type="text"]'
  );
  if (!input) return;

  if (input.type === "password") {
    input.type = "text";
    btn.setAttribute("aria-pressed", "true");
    btn.setAttribute("aria-label", "Ocultar senha");
    // replace svg with eye-off
    btn.innerHTML =
      '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M1 1l22 22" stroke="#fff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M17.94 17.94C16.06 19.2 14.08 20 12 20 5 20 1 12 1 12c1.66-3.45 4.36-5.93 7.53-7.13" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
  } else {
    input.type = "password";
    btn.setAttribute("aria-pressed", "false");
    btn.setAttribute("aria-label", "Mostrar senha");
    // restore eye svg
    btn.innerHTML =
      '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
  }
});

// Clear inputs on load to avoid browser autofill showing previous values
document.addEventListener("DOMContentLoaded", function () {
  // clear login form fields
  const loginForm = document.getElementById("loginForm");
  if (loginForm) {
    const inputs = loginForm.querySelectorAll("input");
    inputs.forEach((i) => {
      i.value = "";
    });
  }

  // clear cadastro form fields
  const cadastroForm = document.getElementById("cadastroForm");
  if (cadastroForm) {
    const inputs = cadastroForm.querySelectorAll("input");
    inputs.forEach((i) => {
      i.value = "";
    });
  }

  // ensure toggle buttons show initial eye svg (in case script loads after render)
  document.querySelectorAll(".toggle-password").forEach((btn) => {
    if (!btn.innerHTML.trim()) {
      btn.innerHTML =
        '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
      btn.setAttribute("aria-pressed", "false");
    }
  });
});
