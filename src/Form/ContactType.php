<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Validator\Constraints as Assert;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'attr' => ['class' => 'form-control'],
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('phone', TelType::class, [
                'label' => 'Téléphone',
                'attr' => ['class' => 'form-control'],
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['class' => 'form-control'],
                'constraints' => [new Assert\NotBlank(), new Assert\Email()],
            ])
            ->add('subject', TextType::class, [
                'label' => 'Objet',
                'attr' => ['class' => 'form-control'],
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('project_type', ChoiceType::class, [
                'label' => 'Type de projet',
                'choices' => [
                    'Escalier' => 'escalier',
                    'Portail' => 'portail',
                    'Garde-corps' => 'garde-corps',
                    'Serrurerie' => 'serrurerie',
                    'Mobilier décoratif' => 'mobilier',
                    'Autre' => 'autre',
                ],
                'placeholder' => 'Choisissez un type de projet',
                'attr' => ['class' => 'form-select'],
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                'attr' => ['class' => 'form-control', 'rows' => 6],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(
                        min: 10,
                        minMessage: 'Votre message doit contenir au moins {{ limit }} caractères.',
                        max: 2000,
                        maxMessage: 'Votre message ne peut pas dépasser {{ limit }} caractères.',
                    ),
                ],
            ])
            // Honeypot invisible pour filtrer les bots
            ->add('honeypot', HiddenType::class, [
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'autocomplete' => 'off',
                    'style' => 'display:none',
                ],
                'constraints' => [
                    new Assert\EqualTo([
                        'value' => '',
                        'message' => 'Spam détecté.',
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => ['class' => 'btn btn-primary mt-3'],
            ]);
    }
}
