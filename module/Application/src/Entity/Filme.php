<?php
namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "filmes")]
class Filme
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 255)]
    private string $nome;

    #[ORM\Column(type: "text")]
    private string $sinopse;

    #[ORM\Column(type: "string", length: 255)]
    private string $capaPrincipal;

    #[ORM\Column(type: "string", length: 255)]
    private string $capaFundo;

    #[ORM\Column(type: "integer")]
    private int $anoLancamento;

    #[ORM\Column(type: "string", length: 255)]
    private string $diretor;

    #[ORM\Column(type: "string", length: 200)]
    private string $elenco;

    #[ORM\Column(type: "string", length: 100)]
    private string $genero;

    #[ORM\Column(type: "float")]
    private float $nota;

    #[ORM\Column(type: "string", length: 500)]
    private string $trailer;

    #[ORM\Column(type: "string", length: 100)]
    private string $streaming;

    // =======================
    // GETTERS e SETTERS
    // =======================
    
    public function getId(): int { return $this->id; }

    public function getNome(): string { return $this->nome; }
    public function setNome(string $nome) { $this->nome = $nome; }

    public function getSinopse(): string { return $this->sinopse; }
    public function setSinopse(string $sinopse) { $this->sinopse = $sinopse; }

    public function getCapaPrincipal(): string { return $this->capaPrincipal; }
    public function setCapaPrincipal(string $capa) { $this->capaPrincipal = $capa; }

    public function getCapaFundo(): string { return $this->capaFundo; }
    public function setCapaFundo(string $capa) { $this->capaFundo = $capa; }

    public function getAnoLancamento(): int { return $this->anoLancamento; }
    public function setAnoLancamento(int $ano) { $this->anoLancamento = $ano; }

    public function getDiretor(): string { return $this->diretor; }
    public function setDiretor(string $diretor) { $this->diretor = $diretor; }

    public function getElenco(): string { return $this->elenco; }
    public function setElenco(string $elenco) { $this->elenco = $elenco; }

    public function getGenero(): string { return $this->genero; }
    public function setGenero(string $genero) { $this->genero = $genero; }

    public function getNota(): float { return $this->nota; }
    public function setNota(float $nota) { $this->nota = $nota; }

    public function getTrailer(): string { return $this->trailer; }
    public function setTrailer(string $trailer) { $this->trailer = $trailer; }

    public function getStreaming(): string { return $this->streaming; }
    public function setStreaming(string $streaming) { $this->streaming = $streaming; }
}
