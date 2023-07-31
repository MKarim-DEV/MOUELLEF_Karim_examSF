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
                    'CDD' => 'cdd',
                    'CDI' => 'cdi',
                    'Interim' => 'interim',
                ],
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner un type de contrat.',
                    ]),
                ],
            ])
            ->add('end_date', DateType::class, [
                // "attr" => [
                //     "class" => "form-control" . ($options['data']->getContract() === 'cdd' || $options['data']->getContract() === 'interim' ? '' : 'd-none'),
                //     "id" => "end_date"
                // ],
                'label' => 'Date de fin de contrat (Jour/Mois/Année)',
                'required' => false,
                'format' => 'dd-MM-yyyy',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => true,
        ]);
    }
}
