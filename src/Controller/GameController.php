<?php
namespace App\Controller;
use App\Entity\Cartes;
use App\Entity\Joueur;
use App\Entity\Partie;
use App\Repository\CartesRepository;
use App\Repository\JoueurRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * Class JeuCopieController
 * @package App\Controller
 * @Route("/jeu")
 */
class GameController extends AbstractController
{

    /**
     * @Route("/", name="jeu")
     */
    public function index()
    {
        return $this->render('jeu/index.html.twig', [
            'user' => $this->getUser()
        ]);
    }

    /**
     * @Route("/nouvelle-partie", name="new_game")
     */
    public function newGame(JoueurRepository $userRepository)
    {
        return $this->render('site/creer_partie.html.twig', [
            'joueur'        => $this->getUser(),
            'adversaires' => $userRepository->findAll()
        ]);
    }
    /**
     * @Route("/creer-partie", name="create_game")
     */
    public function createGame(EntityManagerInterface $entityManager, Request $request, JoueurRepository $userRepository, CartesRepository $carteRepository)
    {
        $joueur1 = $this->getUser();
        $joueur2 = $userRepository->find($request->request->get('adversaire'));
        if ($joueur1 !== null && $joueur2 !== null) {
            $partie = new Partie();

            $partie->setJoueur1($joueur1);
            $partie->setJoueur2($joueur2);
            $partie->setStatut(1);
            $partie->setTour(1);
            $partie->setMove(1);

            $cartes = $carteRepository->findAll();
            $cartesJ1 = [];
            $cartesJ2 = [];
            $shogun1 = null;
            $shogun2 = null;

            foreach ($cartes as $carte) {
                if ($carte->getCamp() === 'J1') {
                    if ($carte->isShogun()) {
                        $shogunJ1 = $carte->getId();
                    } else {
                        $cartesJ1[] = $carte->getId();
                    }
                }
                if ($carte->getCamp() === 'J2') {
                    if ($carte->isShogun()) {
                        $shogunJ2 = $carte->getId();
                    } else {
                        $cartesJ2[] = $carte->getId();
                    }
                }
            }

            shuffle($cartesJ1);
            shuffle($cartesJ2);

            $terrainJ1 = [
                1 => [1 => $shogunJ1, 2 => $cartesJ1[0], 3 => $cartesJ1[1], 4 => $cartesJ1[2]],
                2 => [1 => $cartesJ1[3], 2 => $cartesJ1[4], 3 => $cartesJ1[5]],
                3 => [1 => $cartesJ1[6], 2 => $cartesJ1[7]],
                4 => [1 => $cartesJ1[8]],
                5 => [],
                6 => [],
                7 => [],
                8 => [],
                9 => [],
                10 => [],
                11 => []
            ];

            $terrainJ2 = [
                1 => [1 => $shogunJ2, 2 => $cartesJ2[0], 3 => $cartesJ2[1], 4 => $cartesJ2[2]],
                2 => [1 => $cartesJ2[3], 2 => $cartesJ2[4], 3 => $cartesJ2[5]],
                3 => [1 => $cartesJ2[6], 2 => $cartesJ2[7]],
                4 => [1 => $cartesJ2[8]],
                5 => [],
                6 => [],
                7 => [],
                8 => [],
                9 => [],
                10 => [],
                11 => []
            ];

            $partie->setTerrain1($terrainJ1);
            $partie->setTerrain2($terrainJ2);
            $partie->setDes([]);
            $em = $this->getDoctrine()->getManager();
            $em->persist($partie);
            $em->flush();
            return $this->redirectToRoute('show_game', ['partie' => $partie->getId(), 'tour' => $partie->getTour()]);
        }
    }
    /**
     * @Route("/affiche-partie/{partie}", name="show_game")
     */
    public function showGame(Partie $partie, CartesRepository $carteRepository)
    {
        $cartes = $carteRepository->findAll();
        $tCartes = [];
        foreach ($cartes as $carte)
        {
            $tCartes[$carte->getId()] = $carte;
        }

        if ($partie->getJoueur1()->getId() === $this->getUser()->getId()) {
            //en base c'est J1, adversaire = J2;
            $terrainJoueur = $partie->getTerrain1();
            $terrainAdversaire = $partie->getTerrain2();
        } else {
            $terrainAdversaire = $partie->getTerrain1();
            $terrainJoueur = $partie->getTerrain2();
        }
        return $this->render('jeu/afficher_partie.html.twig', [
            'partie'            => $partie,
            'terrainAdversaire' => $terrainAdversaire,
            'terrainJoueur'     => $terrainJoueur,
            'tCartes'           => $tCartes
        ]);
    }
    /**
     * @param Request $request
     * @Route("/deplacement-partie/{partie}", name="deplacement_game", methods={"GET"})
     */
    public function move(EntityManagerInterface $entityManager, CartesRepository $carteRepository, Request $request, Partie $partie) {
        $carte = $carteRepository->find($request->query->get('id'));

        if ($carte !== null)
        {
            $numPile = $request->query->get('pile');
            $position = $request->query->get('position');
            $valeurDeplacement = $request->query->get('valeur');

            $terrainJoueur = null;
            $terrainAdv = null;

            //vérifie si le joueur connecté est le joueur 1
            if ($this->getUser()->getId() === $partie->getJoueur1()->getId())
            {
                $terrainJoueur = $partie->getTerrain1();
                $terrainAdv = $partie->getTerrain2();
            } else {
                $terrainJoueur = $partie->getTerrain2();
                $terrainAdv = $partie->getTerrain1();
            }

            $pileDepart = $terrainJoueur[$numPile];
            $terrainJoueur[$numPile] = [];

            $pileDestination = $numPile + $valeurDeplacement;
            //fait en sorte que la pile de destination soit entre 1 et 11
            if ($pileDestination > 11)
            {
                $pileDestination = 11;
            }

            $pileDestinationAdv = 11 - $numPile - $valeurDeplacement +1;
            //fait en sorte que la pile de destination du côté adverse soit entre 1 et 11
            if ($pileDestinationAdv < 0)
            {
                $pileDestinationAdv = 0;
            }

            $nbCartes = count($terrainJoueur[$pileDestination]);
            $i = 1;
            if ($nbCartes === 0) {
                $terrainJoueur[$pileDestination] = [];
            }
            //rajoute les cartes dans la pile de destination
            foreach ($pileDepart as $index => $idCarte)
            {
                if ($i >= $position)
                {
                    $terrainJoueur[$pileDestination][$nbCartes + $index] = $idCarte;
                } else {
                    $terrainJoueur[$numPile][$index] = $idCarte;
                }
                $i++;
            }

            if ($this->getUser()->getId() === $partie->getJoueur1()->getId())
            {
                $partie->setTerrain1($terrainJoueur);
            } else {
                $partie->setTerrain2($terrainJoueur);
            }

            $entityManager->flush();

            $plusPetitePile = null;
            if ($pileDestination <= $pileDestinationAdv) {
                $plusPetitePile = $pileDestination;
            } else {
                $plusPetitePile = $pileDestinationAdv;
            }

            if ($pileDestinationAdv > 0)
            {
                if (count($terrainAdv[$pileDestinationAdv]) > 0)
                {
                    $idCombattantJoueur = end($terrainJoueur[$pileDestination]);
                    $idCombattantAdv = end($terrainAdv[$pileDestinationAdv]);
                    return $this->json(['etat' => 'conflit', 'idCombattantJoueur' => $idCombattantJoueur, 'idCombattantAdv' => $idCombattantAdv, 'pileDestination' => $pileDestination, 'pileDestinationAdv' => $pileDestinationAdv, 'taillePileDestination' => count($terrainJoueur[$pileDestination]), 'taillePileDestinationAdv' => count($terrainAdv[$pileDestinationAdv])]);
                } else {
                    return $this->json(['etat' => 'pas de conflit']);
                }
            }
        }
        return $this->json('OK', Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @Route("/resolve-conflict/{partie}", name="resolve_conflict", methods={"GET"})
     */
    public function resolveConflict(CartesRepository $carteRepository, Request $request, Partie $partie, EntityManagerInterface $entityManager)
    {
            $carteJ1 = $carteRepository->find($request->query->get('carteJoueur'));
            $carteJ2 = $carteRepository->find($request->query->get('carteAdv'));
            $pile1 = $request->query->get('pileJoueur');
            $pile2 = $request->query->get('pileAdv');

        if ($carteJ1 && $carteJ2) {
            if ($carteJ1->getArme() == $carteJ2->getArme()) {
                if ($carteJ1->getStr() > $carteJ2->getStr()) {

                    if ($this->getUser()->getId() === $partie->getJoueur1()->getId())
                    {
                        $terrain2 = $partie->getTerrain2();

                    } else {
                        $terrain2 = $partie->getTerrain1();
                    }

                    $tab = array_pop($terrain2[$pile2]);

                    if ($this->getUser()->getId() === $partie->getJoueur1()->getId())
                    {
                        $partie->setTerrain2($terrain2);

                    } else {
                        $partie->setTerrain1($terrain2);
                    }

                    $entityManager->flush();
                } elseif ($carteJ1->getStr() < $carteJ2->getStr()) {

                    if ($this->getUser()->getId() === $partie->getJoueur1()->getId())
                    {
                        $terrain1 = $partie->getTerrain1();

                    } else {
                        $terrain1 = $partie->getTerrain2();
                    }

                    $tab = array_pop($terrain1[$pile1]);

                    if ($this->getUser()->getId() === $partie->getJoueur1()->getId())
                    {
                        $partie->setTerrain1($terrain1);

                    } else {
                        $partie->setTerrain2($terrain1);
                    }

                    $entityManager->flush();
                } elseif ($carteJ1->getStr() === $carteJ2->getStr()) {

                    if ($this->getUser()->getId() === $partie->getJoueur1()->getId())
                    {
                        $terrain1 = $partie->getTerrain1();
                        $terrain2 = $partie->getTerrain2();
                    } else {
                        $terrain1 = $partie->getTerrain2();
                        $terrain2 = $partie->getTerrain1();
                    }

                    $terrain1[$pile1 - 1][] = $carteJ1->getId();
                    array_pop($terrain1[$pile1]);

                    $terrain2[$pile2 - 1][] = $carteJ2->getId();
                    array_pop($terrain2[$pile2]);

                    $partie->setTerrain1($terrain1);
                    $partie->setTerrain2($terrain2);

                    $entityManager->flush();
                }
            }

            if ($carteJ1->getArme() == "couteau" && $carteJ2->getArme() == "dynamite" ||
                $carteJ1->getArme() == "dynamite" && $carteJ2->getArme() == "revolver" ||
                $carteJ1->getArme() == "revolver" && $carteJ2->getArme() == "couteau") {

                if ($this->getUser()->getId() === $partie->getJoueur1()->getId())
                {
                    $terrain2 = $partie->getTerrain2();

                } else {
                    $terrain2 = $partie->getTerrain1();
                }

                $tab = array_pop($terrain2[$pile2]);

                if ($this->getUser()->getId() === $partie->getJoueur1()->getId())
                {
                    $partie->setTerrain2($terrain2);

                } else {
                    $partie->setTerrain1($terrain2);
                }

                $entityManager->flush();
            } elseif ($carteJ2->getArme() == "couteau" && $carteJ1->getArme() == "dynamite" ||
                $carteJ2->getArme() == "dynamite" && $carteJ1->getArme() == "revolver" ||
                $carteJ2->getArme() == "revolver" && $carteJ1->getArme() == "couteau") {

                if ($this->getUser()->getId() === $partie->getJoueur1()->getId())
                {
                    $terrain1 = $partie->getTerrain1();

                } else {
                    $terrain1 = $partie->getTerrain2();
                }

                $tab = array_pop($terrain1[$pile1]);

                if ($this->getUser()->getId() === $partie->getJoueur1()->getId())
                {
                    $partie->setTerrain1($terrain1);

                } else {
                    $partie->setTerrain2($terrain1);
                }

                $entityManager->flush();
            }
        }

        $idCombattantJoueur = end($terrain1[$pile1]);
        $idCombattantAdv = end($terrain2[$pile2]);

        return $this->json(['idDerniereJoueur' => $idCombattantJoueur, 'idDerniereAdv' => $idCombattantAdv, 'taillePileJoueur' => count($terrain1[$pile1]), 'taillePileAdv' => count($terrain2[$pile2])]);
    }

    /**
     * @param Request $request
     * @Route("/refresh-terrain/{partie}", name="refresh_game")
     */
    public function refreshTerrain(CartesRepository $carteRepository, Partie $partie)
    {
        $cartes = $carteRepository->findAll();
        $tCartes = [];
        foreach ($cartes as $carte)
        {
            $tCartes[$carte->getId()] = $carte;
        }

        if ($partie->getJoueur1()->getId() === $this->getUser()->getId()) {
            //en base c'est J1, adversaire = J2;
            $terrainJoueur = $partie->getTerrain1();
            $terrainAdversaire = $partie->getTerrain2();
        } else {
            $terrainAdversaire = $partie->getTerrain1();
            $terrainJoueur = $partie->getTerrain2();
        }

        return $this->render('jeu/plateau.html.twig', [
            'partie'            => $partie,
            'terrainAdversaire' => $terrainAdversaire,
            'terrainJoueur'     => $terrainJoueur,
            'tCartes'           => $tCartes
        ]);
    }

    /**
     * @param Request $request
     * @Route("/which-turn/{partie}", name="which_turn")
     */
    public function whichTurn(Partie $partie)
    {
        if ($this->getUser()->getId() === $partie->getJoueur1()->getId() && $partie->getTour() === 1)
        {
            return $this->json(['montour' => true, 'tour' => $partie->getTour()]);
        } elseif ($this->getUser()->getId() === $partie->getJoueur1()->getId() && $partie->getTour() === 2) {
            return $this->json(['montour' => false, 'tour' => $partie->getTour()]);
        }

        if ($this->getUser()->getId() === $partie->getJoueur2()->getId() && $partie->getTour() === 2)
        {
        return $this->json(['montour' => true, 'tour' => $partie->getTour()]);
        } elseif ($this->getUser()->getId() === $partie->getJoueur2()->getId() && $partie->getTour() === 1) {
            return $this->json(['montour' => false, 'tour' => $partie->getTour()]);
        }
        return $this->json('ok');
    }

    /**
     * @param Request $request
     * @Route("/change-turn/{partie}", name="change_turn")
     */
    public function changeTurn(Partie $partie, EntityManagerInterface $entityManager)
    {
        if ($partie->getTour() === 1)
        {
            $partie->setTour(2);
            $entityManager->flush();
        } else {
            $partie->setTour(1);
            $entityManager->flush();
        }

        return $this->json($partie->getTour(), Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @Route("/which-move/{partie}", name="which_move")
     */
    public function whichRoute(Partie $partie)
    {
        return $this->json($partie->getMove());
    }

    /**
     * @param Request $request
     * @Route("/change-move/{partie}", name="change_move")
     */
    public function changeMove(Partie $partie, EntityManagerInterface $entityManager)
    {
        if ($partie->getMove() === 1)
        {
            $partie->setMove(2);
            $entityManager->flush();
        } else {
            $partie->setMove(1);
            $entityManager->flush();
        }
        return $this->json('OK', Response::HTTP_OK);
    }
}