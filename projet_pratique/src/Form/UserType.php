<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('firstName', TextType::class, [
        'label' => 'Prénom',
        'constraints' => [
          new NotBlank(['message' => 'Veuillez entrer votre prénom']),
        ],
      ])
      ->add('lastName', TextType::class, [
        'label' => 'Nom',
        'constraints' => [
          new NotBlank(['message' => 'Veuillez entrer votre nom']),
        ],
      ])
      ->add('email', EmailType::class, [
        'label' => 'Email',
        'constraints' => [
          new NotBlank(['message' => 'Veuillez entrer votre adresse email']),
        ],
      ])
      ->add('password', PasswordType::class, [
        'label' => 'Mot de passe',
        'required' => true,
      ])
      ->add('submit', SubmitType::class, [
        'label' => 'Mettre à jour',
        'attr' => ['class' => 'btn primary-button'],
      ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'data_class' => User::class,
    ]);
  }
}
