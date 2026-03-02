<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangeEmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
                'label' => 'Mot de passe actuel',
                'attr' => ['placeholder' => 'Votre mot de passe actuel', 'class' => 'form-control']
            ])
            ->add('newEmail', EmailType::class, [
                'label' => 'Nouvelle adresse email',
                'attr' => ['placeholder' => 'nouvel.email@exemple.com', 'class' => 'form-control']
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Modifier l\'email',
                'attr' => ['class' => 'btn btn-primary btn-lg mt-3']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}