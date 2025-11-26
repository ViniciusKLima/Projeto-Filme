document
  .getElementById("cadastroForm")
  .addEventListener("submit", function (e) {
    e.preventDefault();

    const usuario = document.getElementById("usuario").value;
    const email = document.getElementById("email").value;
    const senha = document.getElementById("senha").value;

    if (!usuario || !email || !senha) {
      alert("Todos os campos são obrigatórios!");
      return;
    }

    const usuarios = JSON.parse(localStorage.getItem("usuarios")) || [];

    // Verifica se o email já está cadastrado
    if (usuarios.some((u) => u.email === email)) {
      alert("Este email já está cadastrado!");
      return;
    }

    // Cria objeto do novo usuário (sempre tipo cliente)
    const novoUsuario = { usuario, email, senha, tipoConta: "cliente" };

    usuarios.push(novoUsuario);
    localStorage.setItem("usuarios", JSON.stringify(usuarios));

    alert("Cadastro realizado com sucesso!");
    window.location.href = "../Login/Login.html";
  });
