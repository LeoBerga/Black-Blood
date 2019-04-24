<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PartieRepository")
 */
class Partie
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $statut;

    /**
     * @ORM\Column(type="integer")
     */
    private $tour;

    /**
     * @ORM\Column(type="text")
     */
    private $des = [];

    /**
     * @ORM\Column(type="text")
     */
    private $terrain1 = [];

    /**
     * @ORM\Column(type="text")
     */
    private $terrain2 = [];

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $duree;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fin;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type_victoire;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Tchat", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $_tchat;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $vainqueur;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Joueur", inversedBy="parties")
     */
    private $joueur1;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Joueur", inversedBy="parties2")
     */
    private $joueur2;

    /**
     * @ORM\Column(type="integer")
     */
    private $move;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatut(): ?int
    {
        return $this->statut;
    }

    public function setStatut(int $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getTour(): ?int
    {
        return $this->tour;
    }

    public function setTour(int $tour): self
    {
        $this->tour = $tour;

        return $this;
    }

    public function getDes()
    {
        return json_decode($this->des);
    }

    public function setDes($des): self
    {
        $this->des = json_encode($des);

        return $this;
    }

    public function getTerrain1(): array
    {
        $terrain1 =  json_decode($this->terrain1, true);

        return $terrain1;
    }

    public function setTerrain1(array $terrain1): self
    {
        $this->terrain1 = json_encode($terrain1);

        return $this;
    }

    public function getTerrain2(): array
    {
        $terrain2 =  json_decode($this->terrain2, true);

        return $terrain2;
    }

    public function setTerrain2(array $terrain2): self
    {
        $this->terrain2 = json_encode($terrain2);

        return $this;
    }

    public function getDuree(): ?\DateTimeInterface
    {
        return $this->duree;
    }

    public function setDuree(\DateTimeInterface $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getFin(): ?\DateTimeInterface
    {
        return $this->fin;
    }

    public function setFin(?\DateTimeInterface $fin): self
    {
        $this->fin = $fin;

        return $this;
    }

    public function getTypeVictoire(): ?string
    {
        return $this->type_victoire;
    }

    public function setTypeVictoire(?string $type_victoire): self
    {
        $this->type_victoire = $type_victoire;

        return $this;
    }

    public function getTchat(): ?Tchat
    {
        return $this->_tchat;
    }

    public function setTchat(Tchat $_tchat): self
    {
        $this->_tchat = $_tchat;

        return $this;
    }

    public function getVainqueur(): ?string
    {
        return $this->vainqueur;
    }

    public function setVainqueur(?string $vainqueur): self
    {
        $this->vainqueur = $vainqueur;

        return $this;
    }

    public function getJoueur1(): ?Joueur
    {
        return $this->joueur1;
    }

    public function setJoueur1(?Joueur $joueur1): self
    {
        $this->joueur1 = $joueur1;

        return $this;
    }

    public function getJoueur2(): ?Joueur
    {
        return $this->joueur2;
    }

    public function setJoueur2(?Joueur $joueur2): self
    {
        $this->joueur2 = $joueur2;

        return $this;
    }

    public function getMove(): ?int
    {
        return $this->move;
    }

    public function setMove(int $move): self
    {
        $this->move = $move;

        return $this;
    }
}
