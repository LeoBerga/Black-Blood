<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CartesRepository")
 */
class Cartes
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $arme;

    /**
     * @ORM\Column(type="integer")
     */
    private $str;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $camp;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $image;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getArme(): ?string
    {
        return $this->arme;
    }

    public function setArme(string $arme): self
    {
        $this->arme = $arme;

        return $this;
    }

    public function getStr(): ?int
    {
        return $this->str;
    }

    public function setStr(int $str): self
    {
        $this->str = $str;

        return $this;
    }

    public function getCamp(): ?string
    {
        return $this->camp;
    }

    public function setCamp(string $camp): self
    {
        $this->camp = $camp;

        return $this;
    }

    public function isShogun()
    {
        return $this->getStr() === 4;
    }

    public function isRevolver()
    {
        return $this->getArme() === 'revolver';
    }

    public function isDynamite()
    {
        return $this->getArme() === 'dynamite';
    }

    public function isCouteau()
    {
        return $this->getArme() === 'couteau';
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }
}
