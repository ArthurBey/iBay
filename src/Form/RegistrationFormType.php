<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                "label" => "Prénom",
                "attr" => ['placeholder' => "Votre prénom"]
            ])
            ->add('nickname', TextType::class, [
                "label" => "Nom d'utilisateur",
                "attr" => ['placeholder' => "Votre nom d'utilisateur"]
            ])
            ->add('lastName', TextType::class, [
                "label" => "Nom",
                "attr" => ['placeholder' => "Votre nom de famille"]
            ])
            ->add('email', EmailType::class, [
                "label" => "Email",
                "attr" => ['placeholder' => "Votre adresse email"]
                ])
            ->add('description', TextareaType::class, [
                "label" => "Description de votre profil",
                "attr" => ['placeholder' => "Présentez-vous à vos futurs acheteurs/vendeurs !"]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('profilePicture', UrlType::class, [
                "label" => "Votre photo de profile (falcultatif)",
                "attr" => ['placeholder' => "Lien URL de votre photo de profile"],
                "required" => false
                ])
            ->add('description', TextareaType::class, [
                "label" => "Description de votre profil",
                "attr" => ['placeholder' => "Présentez-vous à vos futurs acheteurs/vendeurs !"]
            ])
            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'type' => PasswordType::class,
                'invalid_message' => "Les mots de passe ne correspondent pas",
                'mapped' => false,
                'required' => true,
                'first_options' => [
                    "label" => "Saisissez un mot de passe",
                    "attr" => ["placeholder" => "Votre mot de passe"]
                ],
                'second_options' => [
                    "label" => "Répetez le mot de passe",
                    "attr" => ["placeholder" => "Votre mot de passe"]
                ]/*, 
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Le mot de passe doit contenir un minimum de {{ limit }} caractères ',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096
                    ])
                ] */
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
