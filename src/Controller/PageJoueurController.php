<?php
namespace App\Controller;

use App\Entity\Joueur;
use App\Form\RegisterType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class PageJoueurController extends AbstractController
{
    /**
     * @Route("/page_joueur", name="page_joueur")
     */
    public function index(UserInterface $user)
    {
        $user->setDerniereConnexion(new \DateTime('+1 hour') );
        $user->setEnLigne(true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $username = $user->getPseudo();
        $nb_victoires = $user->getNbVictoires();
        $nb_defaites = $user->getNbDefaites();
        $nb_parties = $nb_victoires+$nb_defaites;
        $derniere_co = $user->getDerniereConnexion();

        return $this->render('site/page_joueur.html.twig', array(
            'username' => $username,
            'email' => $this->getUser()->getEmail(),
            'victoires' => $nb_victoires,
            'defaites' => $nb_defaites,
            'parties' => $nb_parties,
            'derniere_co' => $derniere_co->format('d/m/Y à H:i')
        ));
    }

    /**
     * @Route("/changer_infos", name="app_new_info")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, UserInterface $joueur)
    {

        $form = $this->createForm(RegisterType::class, $joueur);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $password = $passwordEncoder->encodePassword($joueur, $joueur->getPassword());
            $joueur->setPassword($password);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($joueur);
            $entityManager->flush();
        }

        return $this->render(
            'security/newInfos.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     *  @Route("/blackblood/page_joueur/amis", name="demande_amis")
     */
    public function ajouterAmis(Request $request, \Swift_Mailer $mailer, TokenGeneratorInterface $tokenGenerator)
    {
        $perso = $this->getUser();

        if ($request->isMethod('POST'))
        {
            $email = $request->request->get('email');
            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(Joueur::class)->findOneByEmail($email);
            if ($user === null)
            {
                $this->addFlash('danger', 'Email Inconnu');
            }
            $token = $tokenGenerator->generateToken();
            try
            {
                $user->setResetToken($token);
                $entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
            }

            $url = $this->generateUrl('accepter_demande', array('token' => $token),UrlGeneratorInterface::ABSOLUTE_URL);
            $message = (new \Swift_Message('Demande d\'amis'))
                ->setFrom('ufagencyblackblood@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $perso->getPseudo()." vous a demandé en ami ! Cliquez sur ce lien pour accepter ou sinon ignorez ce message." . $url.'?idJoueur='.$perso->getId(),
                    'text/html'
                );
            $mailer->send($message);
            $this->addFlash('notice', 'Demande envoyée');
            return $this->redirectToRoute('page_joueur');
        }
        return $this->render('site/demande_amis.html.twig');
    }

    /**
     * @Route("/blackblood/page_joueur/amis/valider{token}", name="accepter_demande")
     */
    public function accepterDemande(Request $request, string $token)
    {
        $id = intval($_GET['idJoueur']);
        if ($request->isMethod('POST')) {
            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(Joueur::class)->findOneByResetToken($token);
            $user2 = $entityManager->getRepository(Joueur::class)->find($id);
            if ($user === null) {
                $this->addFlash('danger', 'Token Inconnu');
                return $this->redirectToRoute('app_login');
            }

            $user->setResetToken(null);
            if($request->request->get('valider')){
                $tab = $user->getListeAmis();
                $tab2 = $user2->getListeAmis();
                var_dump($tab);
                var_dump($tab2);
                array_push($tab,$id);
                array_push($tab2,$user->getId());
                $user->setListeAmis($tab);
                $user2->setListeAmis($tab2);

            }

            $entityManager->flush();

            return $this->redirectToRoute('app_login');

        }else {
            return $this->render('site/valider_friend.html.twig', ['token' => $token,'id' => $id]);
        }
    }
}
?>