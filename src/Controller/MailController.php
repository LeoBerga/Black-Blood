<?php
// src/Controller/MailController.php
namespace App\Controller;

use App\Entity\Joueur;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class MailController extends AbstractController
{
    /**
     * @Route("/mail/confirm", name="mail_confirmation")
     */
    public function mailConfirmation(\Swift_Mailer $mailer): Response
    {
        $message = (new \Swift_Message('Inscription Black Blood'))
            ->setFrom('ufagencyblackblood@gmail.com')
            ->setTo($_GET['email'])
            ->setBody(
                "<div style='padding-left: 100px'><h1>Félicitations !</h1><p>Tu viens de rejoindre le monde de Black Blood !</p></div>",
                'text/html'
            );

        $mailer->send($message);

        return $this->redirectToRoute('app_accueil');
    }

    /**
     * @Route("/mail/avertir", name="mail_avertir")
     */
    public function mailAvertissement(\Swift_Mailer $mailer): Response
    {
        $message = (new \Swift_Message('Avertissement Black Blood'))
            ->setFrom('ufagencyblackblood@gmail.com')
            ->setTo($_GET['email'])
            ->setBody(
                "<div style='padding-left: 100px'><h1 style='color: red'>Attention !</h1><p>Il semblerait qu'un joueur ai eu des ennuis avec toi.</p><p>Si cela se reproduit, tu seras banni.</p></div>",
                'text/html'
            );

        $mailer->send($message);

        return $this->redirectToRoute('easyadmin', array());
    }

    /**
     * @Route("/mail/contact", name="mail_contact")
     */
    public function mailContact(Request $request, \Swift_Mailer $mailer)
    {
        $listeMail = [];

        $entity = $this->getDoctrine()->getRepository(Joueur::class)->findAll();
        foreach ($entity as $item)
        {
            array_push($listeMail, $item->getEmail());
        }

        $form = $this->createFormBuilder()
            ->add('objet', TextType::class, ['label' => ' ', 'attr' => ['placeholder' => 'Objet']])
            ->add('message', TextareaType::class, ['label' => ' ', 'attr' => ['placeholder' => 'Message']])
            ->add('joueur', ChoiceType::class, [
                'choices' => $listeMail,
                'choice_label' => function ($value)
                {
                    return $value;
                }
            ])
            ->add('everyone', CheckboxType::class, ['label' => 'Envoyer à tout les joueurs', 'required' => false])
            ->add('save', SubmitType::class, array('label' => 'Envoyer'))
            ->getForm();


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            if ($form['everyone']->getData() == true)
            {
                $message = (new \Swift_Message($form['objet']->getData()))
                    ->setFrom('ufagencyblackblood@gmail.com')
                    ->setTo($listeMail)
                    ->setBody(
                        $form['message']->getData(),
                        'text/html'
                    );
            } else {
                $message = (new \Swift_Message($form['objet']->getData()))
                    ->setFrom('ufagencyblackblood@gmail.com')
                    ->setTo($form['joueur']->getData())
                    ->setBody(
                        $form['message']->getData(),
                        'text/html'
                    );
            }

            $mailer->send($message);

            return $this->redirectToRoute('app_back', array());
        }

        return $this->render('back/contact.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
?>