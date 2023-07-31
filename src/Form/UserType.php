<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotBlankValidator;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            // ->add('roles')
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label' => 'Password',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password',
                "class" => "form-control"],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    new Regex([
                        //                        'pattern' => '/^(?=.[A-Za-z])(?=.\d).{8,}$/',
                        'pattern' => '/(?=\S*[a-z])(?=\S*\d)/',
                        //                    
                        'message' => 'Your password should contain at least 1 number and 1 letter',
                    ]),
                ],
            ])
            ->add('name', TextType::class, [
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('firstname', TextType::class, [
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('picture', FileType::class, [
                "attr" => [
                    "class" => "form-control"
                ],
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => 'Nous acceptons seulement les JPG et les PNG',
                    ])
                ],
            ])
            ->add('division', ChoiceType::class, [
                "attr" => [
                    "class" => "form-control"
                ],
                'label' => 'Secteur d\'activité',
                'choices' => [
                    'RH' => 'rh',
                    'Informatique' => 'info',
                    'Comptabilité' => 'compta',
                    'Direction' => 'dir',
                ]
            ])
            ->add('contract', ChoiceType::class, [
                "attr" => [
                    "class" => "form-control",
                    "id" => "contract"
                ],
                'label' => 'Type de contrat',
                'choices' => [
                    'CDD' => 'CDD',
                    'CDI' => 'CDI',
                    'Interim' => 'INTERIM',
                ],
                'constraints' => [
                    new Choice([
                        'choices' => ['CDI', 'CDD', 'INTERIM'],
                        'message' => 'Veuillez sélectionner un contrat valide.',
                    ]),
                ],
            ])
            ->add('end_date', DateType::class, [
                 "attr" => [
                    "class" => "form-control",
                     "id" => "end_date"
                 ],
                'label' => 'Date de fin de contrat',
                'required' => false,
                'format' => 'dd-MM-yyyy',
            ]);
            $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $user = $event->getData();
                $form = $event->getForm();
                if (isset($user['contract']) && in_array($user['contract'], ['CDD', 'INTERIM'])) {
                    $form->add('end_date', DateType::class, [
                        'label' => 'Date de fin de contrat',
                        'required' => false,
                        'format' => 'dd-MM-yyyy'
                    ]);
                };
            });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => true,
        ]);
    }
}
