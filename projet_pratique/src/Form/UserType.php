<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

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
        'required' => false,
        'mapped' => false,
      ])
      ->add('newPassword', PasswordType::class, [
        'label' => 'Choisissez un nouveau mot de passe',
        'required' => false,
        'mapped' => false,
        'constraints' => [
          new Regex([
            'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            'message' => 'Votre mot de passe doit contenir au moins une lettre minuscule, une lettre majuscule, un chiffre et un caractère spéciaux.',
          ]),
        ],
      ])
      ->add('newPasswordConfirm', PasswordType::class, [
        'label' => 'Confirmez votre nouveau mot de passe',
        'required' => false,
        'mapped' => false,
      ])
      ->add('submit', SubmitType::class, [
        'label' => 'Mettre à jour',
        'attr' => ['class' => 'btn primary-button'],
      ]);

    $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
      $form = $event->getForm();
      $data = $form->getData();

      $password = $form->get('password')->getData();
      $newPassword = $form->get('newPassword')->getData();
      $newPasswordConfirm = $form->get('newPasswordConfirm')->getData();

      if (!empty($password)) {
        if (empty($newPassword)) {
          $form->get('newPassword')->addError(new FormError('Ce champ est obligatoire.'));
        }
        if (empty($newPasswordConfirm)) {
          $form->get('newPasswordConfirm')->addError(new FormError('Ce champ est obligatoire.'));
        }
        if ($newPassword !== $newPasswordConfirm) {
          $form->get('newPasswordConfirm')->addError(new FormError('Les mots de passe ne correspondent pas.'));
        }
      }
    });
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'data_class' => User::class,
    ]);
  }
}
