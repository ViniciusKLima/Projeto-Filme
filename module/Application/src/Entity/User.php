<?php
namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'usuarios')]
class User
{
    /**
     * Entidade User (mapeada para a tabela `usuarios`)
     *
     * Campos principais:
     * - id, nome, email, senha, tipoConta
     * - rememberToken: token opcional para persistência do login (remember-me)
     *
     * Observações:
     * - As senhas são armazenadas usando password_hash() no momento do cadastro.
     * - Em produção é recomendado armazenar apenas o HASH do remember token
     *   (ex.: hash('sha256', $token)) em vez do token em texto puro.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 150)]
    private string $nome;

    #[ORM\Column(type: 'string', length: 150, unique: true)]
    private string $email;

    #[ORM\Column(type: 'string', length: 255)]
    private string $senha;

    #[ORM\Column(type: 'string', length: 20)]
    private string $tipoConta; // "admin" ou "cliente"

    #[ORM\Column(type: 'string', length: 128, nullable: true)]
    private ?string $rememberToken = null;

    // GETTERS E SETTERS
    public function getId(): int { return $this->id; }

    public function getNome(): string { return $this->nome; }
    public function setNome(string $nome): void { $this->nome = $nome; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): void { $this->email = $email; }

    public function getSenha(): string { return $this->senha; }
    public function setSenha(string $senha): void { $this->senha = $senha; }

    public function getTipoConta(): string { return $this->tipoConta; }
    public function setTipoConta(string $tipoConta): void { $this->tipoConta = $tipoConta; }

    public function getRememberToken(): ?string { return $this->rememberToken; }
    public function setRememberToken(?string $token): void { $this->rememberToken = $token; }
}