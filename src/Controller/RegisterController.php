<?php
// src/Controller/RegisterController.php
namespace App\Controller;

use App\Form\RegisterType;
use App\Entity\Joueur;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class RegisterController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $joueur = new Joueur();
        $form = $this->createForm(RegisterType::class, $joueur);
        $joueur->setDateInscription(new \DateTime('now'));
        $joueur->setDerniereConnexion(new \DateTime('now'));
        $joueur->setNbVictoires(0);
        $joueur->setNbDefaites(0);
        $joueur->setBanni(false);
        $joueur->setRoles(['ROLE_USER']);
        $joueur->setEnLigne(false);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $password = $passwordEncoder->encodePassword($joueur, $joueur->getPassword());
            $joueur->setPassword($password);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($joueur);
            $entityManager->flush();

            return $this->redirect('/game/public/index.php/mail/confirm?email='.$form['email']->getData());
        }

        return $this->render(
            'security/register.html.twig',
            array('form' => $form->createView())
        );
    }
}
?>