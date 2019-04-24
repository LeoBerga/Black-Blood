<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Captcha\Bundle\CaptchaBundle\Validator\Constraints as CaptchaAssert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\JoueurRepository")
 */
class Joueur implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="text")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pseudo;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_inscription;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $derniere_connexion;

    /**
     * @ORM\Column(type="bigint")
     */
    private $nb_victoires;

    /**
     * @var string le token qui servira lors de l'oubli de mot de passe
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $resetToken;

    /**
     * @ORM\Column(type="boolean")
     */
    private $banni;

    /**
     * @ORM\Column(type="bigint")
     */
    private $nb_defaites;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $liste_amis = [];

    /**
     * @ORM\Column(type="boolean")
     */
    private $enligne;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $avertissements;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = json_decode($this->roles, true);
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = json_encode($roles);

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->date_inscription;
    }

    public function setDateInscription(\DateTimeInterface $date_inscription): self
    {
        $this->date_inscription = $date_inscription;

        return $this;
    }

    public function getDerniereConnexion(): ?\DateTimeInterface
    {
        return $this->derniere_connexion;
    }

    public function setDerniereConnexion(?\DateTimeInterface $derniere_connexion): self
    {
        $this->derniere_connexion = $derniere_connexion;

        return $this;
    }

    public function getNbVictoires(): ?int
    {
        return $this->nb_victoires;
    }

    public function setNbVictoires(int $nb_victoires): self
    {
        $this->nb_victoires = $nb_victoires;

        return $this;
    }

    public function getNbDefaites(): ?int
    {
        return $this->nb_defaites;
    }

    public function setNbDefaites(int $nb_defaites): self
    {
        $this->nb_defaites = $nb_defaites;

        return $this;
    }

    public function getBanni(): ?bool
    {
        return $this->banni;
    }

    public function setBanni(bool $banni): self
    {
        $this->banni = $banni;

        return $this;
    }

    public function getListeAmis(): array
    {
        return json_decode($this->liste_amis);
    }

    public function setListeAmis(array $liste_amis): self
    {
        $this->liste_amis = json_encode($liste_amis);

        return $this;
    }

    public function getEnLigne(): ?bool
    {
        return $this->enligne;
    }

    public function setEnLigne(bool $enligne): self
    {
        $this->enligne = $enligne;

        return $this;
    }


    //CAPTCHA
    /**
     * @CaptchaAssert\ValidCaptcha(
     *      message = "Vérification invalide"
     * )
     */
    protected $captchaCode;

    public function __construct()
    {
        $this->joueur2 = new ArrayCollection();
        $this->parties = new ArrayCollection();
        $this->parties2 = new ArrayCollection();
    }

    public function getCaptchaCode()
    {
        return $this->captchaCode;
    }

    public function setCaptchaCode($captchaCode)
    {
        $this->captchaCode = $captchaCode;
    }



    //RESET MDP
    /**
     * @return string
     */
    public function getResetToken(): string
    {
        return $this->resetToken;
    }

    /**
     * @param string $resetToken
     */
    public function setResetToken(?string $resetToken): void
    {
        $this->resetToken = $resetToken;
    }

    public function getAvertissements(): ?int
    {
        return $this->avertissements;
    }

    public function setAvertissements(?int $avertissements): self
    {
        $this->avertissements = $avertissements;

        return $this;
    }
}
