<?php
// src/Controller/BackController.php
namespace App\Controller;


use App\Entity\Partie;
use App\Entity\Joueur;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;

class BackController extends AbstractController
{
    /**
     * @Route("/back", name="app_back")
     */
    public function index(UserInterface $user)
    {
        $user->setDerniereConnexion(new \DateTime('+1 hour') );
        $user->setEnLigne(true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $en_attente = sizeof($this->getDoctrine()->getRepository(Partie::class)->findByStatut(0));
        $en_cours = sizeof($this->getDoctrine()->getRepository(Partie::class)->findByStatut(1));
        $terminees = sizeof($this->getDoctrine()->getRepository(Partie::class)->findByStatut(2));

        $joueur_inscrits = sizeof($this->getDoctrine()->getRepository(Joueur::class)->findAll());
        $joueur_connectes = sizeof($this->getDoctrine()->getRepository(Joueur::class)->findByEnligne(true));
        $joueur_bloques = sizeof($this->getDoctrine()->getRepository(Joueur::class)->findByBanni(true));

        return $this->render('back/accueil.html.twig', array(
            'en_cours' => $en_cours,
            'terminees' => $terminees,
            'en_attente' => $en_attente,
            'joueurs_inscrits' => $joueur_inscrits,
            'joueur_bloques' => $joueur_bloques,
            'joueur_connectes' => $joueur_connectes
        ));
    }
}
?>