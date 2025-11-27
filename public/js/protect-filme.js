/*
 * protect-filme.js
 *
 * Intercepta cliques nas cards de filme e impede a navegação para a
 * página de detalhes quando o usuário não está autenticado. Em vez disso
 * mostra um modal (estilizado via `public/css/modal.css`) com opção de
 * fazer login ou cancelar.
 */

(function () {
  function createModal(message) {
    var modal = document.createElement("div");
    modal.className = "pf-modal-overlay";
    modal.innerHTML =
      '\n      <div class="pf-modal">\n        <h2>Conteúdo restrito</h2>\n        <p class="pf-modal-msg">' +
      message +
      '</p>\n        <div class="pf-modal-actions">\n          <button class="pf-btn pf-btn-primary" data-action="login">Fazer login</button>\n          <button class="pf-btn" data-action="cancel">Cancelar</button>\n        </div>\n      </div>';
    return modal;
  }

  function showModal(message, onLogin) {
    // Remove quaisquer modais existentes antes de criar um novo. Isso evita
    // empilhar overlays quando a função for chamada múltiplas vezes.
    var existing = document.querySelectorAll(".pf-modal-overlay");
    existing.forEach(function (el) {
      el.parentNode && el.parentNode.removeChild(el);
    });

    var modal = createModal(message);
    document.body.appendChild(modal);

    // O CSS do modal agora vive em public/css/modal.css; aqui apenas tratamos
    // eventos de clique dos botões do modal. Ao fechar, removemos todos os
    // overlays imediatamente para garantir desaparecimento instantâneo.
    modal.addEventListener("click", function (e) {
      var btn = e.target.closest("[data-action]");
      if (!btn) return;
      var action = btn.getAttribute("data-action");
      if (action === "login") {
        // remove todos os modais
        document.querySelectorAll(".pf-modal-overlay").forEach(function (el) {
          el.parentNode && el.parentNode.removeChild(el);
        });
        if (typeof onLogin === "function") onLogin();
      } else if (action === "cancel") {
        document.querySelectorAll(".pf-modal-overlay").forEach(function (el) {
          el.parentNode && el.parentNode.removeChild(el);
        });
      }
    });
  }

  // Inicializa a proteção nas cards que possuem data-href
  function initProtect() {
    document
      .querySelectorAll(".card-filme[data-href]")
      .forEach(function (card) {
        card.addEventListener("click", function (e) {
          var href = card.getAttribute("data-href");
          // se o front-end já sabe que o usuário está logado, segue para detalhes
          if (window.__USER_LOGGED) {
            window.location.href = href;
            return;
          }

          // caso contrário mostra modal com opção de ir ao login
          var msg =
            "Este conteúdo é acessível apenas para usuários com conta. Faça login para continuar e acessar detalhes deste filme.";
          showModal(msg, function () {
            window.location.href = "/auth/login";
          });
        });
      });
  }

  document.addEventListener("DOMContentLoaded", initProtect);
})();
