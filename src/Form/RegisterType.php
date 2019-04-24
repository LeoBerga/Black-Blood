<?php

// src/Form/RegisterType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Captcha\Bundle\CaptchaBundle\Form\Type\CaptchaType;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo', TextType::class, ['label' => false, 'attr' => ['placeholder' => 'PseudO']])
            ->add('email', EmailType::class, ['label' => false, 'attr' => ['placeholder' => 'EmaiL']])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => ['label' => false, 'attr' => ['placeholder' => 'Mot de passE']],
                'second_options' => ['label' => false, 'attr' => ['placeholder' => 'Repetez mot de passE']],
            ])
            ->add('captchaCode', CaptchaType::class, ['captchaConfig' => 'ExampleCaptcha', 'label' => 'Captcha'])
            ->add('save', SubmitType::class, ['label' => 'ValideR']);
    }
}

?>