<?php

// src/Controller/AdminController.php
namespace App\Controller;

use App\Entity\Joueur;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;


class AdminController extends EasyAdminController
{
    public function BannirAction()
    {
        $id = $this->request->query->get('id');
        $entity = $this->em->getRepository(Joueur::class)->find($id);
        $entity->setBanni(true);
        $this->em->flush();

        return $this->redirectToRoute('easyadmin', array(
            'action' => 'list',
            'entity' => $this->request->query->get('entity'),
        ));
    }

    public function debannirAction()
    {
        $id = $this->request->query->get('id');
        $entity = $this->em->getRepository(Joueur::class)->find($id);
        $entity->setBanni(false);
        $this->em->flush();

        return $this->redirectToRoute('easyadmin', array(
            'action' => 'list',
            'entity' => $this->request->query->get('entity'),
        ));
    }

    public function AvertirAction()
    {
        $id = $this->request->query->get('id');
        $entity = $this->em->getRepository(Joueur::class)->find($id);
        $email = $entity->getEmail();
        $entity->setAvertissements($entity->getAvertissements()+1);
        $this->em->flush();

        return $this->redirect('/game/public/index.php/mail/avertir?email='.$email);
    }
}

?>