// password-toggle.js
// - toggles password visibility when button .toggle-password is clicked
// - clears inputs on load (with small timeout to avoid browser autofill race)
// - ensures SVG icons are set

/*
 * password-toggle.js
 *
 * Pequeno utilitário client-side que:
 * - alterna visibilidade do campo de senha (mostrar/ocultar)
 * - limpa inputs dos formulários de login/cadastro no carregamento
 * - garante que os botões de toggle exibam um ícone SVG padrão
 *
 * Uso: incluído em templates de login e cadastro.
 */

(function () {
  function makeEyeSvg() {
    return (
      '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">' +
      '<path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>' +
      '<circle cx="12" cy="12" r="3" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>' +
      "</svg>"
    );
  }

  function makeEyeOffSvg() {
    return (
      '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">' +
      '<path d="M1 1l22 22" stroke="#fff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>' +
      '<path d="M17.94 17.94C16.06 19.2 14.08 20 12 20 5 20 1 12 1 12c1.66-3.45 4.36-5.93 7.53-7.13" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>' +
      "</svg>"
    );
  }

  function togglePassword(btn) {
    var container = btn.parentElement;
    if (!container) return;
    var input = container.querySelector(".password-input");
    if (!input) return;

    if (input.type === "password") {
      input.type = "text";
      btn.setAttribute("aria-pressed", "true");
      btn.setAttribute("aria-label", "Ocultar senha");
      btn.innerHTML = makeEyeOffSvg();
    } else {
      input.type = "password";
      btn.setAttribute("aria-pressed", "false");
      btn.setAttribute("aria-label", "Mostrar senha");
      btn.innerHTML = makeEyeSvg();
    }
  }

  document.addEventListener("click", function (e) {
    var btn = e.target.closest && e.target.closest(".toggle-password");
    if (!btn) return;
    togglePassword(btn);
  });

  function clearFormInputs() {
    // Clear known forms: loginForm and cadastroForm
    var loginForm = document.getElementById("loginForm");
    if (loginForm) {
      var inputs = loginForm.querySelectorAll("input");
      inputs.forEach(function (i) {
        i.value = "";
      });
    }

    var cadastroForm = document.getElementById("cadastroForm");
    if (cadastroForm) {
      var inputs2 = cadastroForm.querySelectorAll("input");
      inputs2.forEach(function (i) {
        i.value = "";
      });
    }
  }

  function ensureToggleButtons() {
    document.querySelectorAll(".toggle-password").forEach(function (btn) {
      if (!btn.innerHTML.trim()) btn.innerHTML = makeEyeSvg();
      if (!btn.hasAttribute("aria-pressed"))
        btn.setAttribute("aria-pressed", "false");
    });
  }

  document.addEventListener("DOMContentLoaded", function () {
    // initial setup
    ensureToggleButtons();
    // clear inputs twice: immediately and after short timeout (works around autofill race)
    clearFormInputs();
    setTimeout(clearFormInputs, 120);
  });
})();
