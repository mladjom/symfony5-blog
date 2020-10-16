<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotNull;

class UserType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['data'] ?? null;
        $isEdit = $user && $user->getId();

        $imageConstraints = [
            new Image([
                'maxSize' => '1024k',
                'mimeTypes' => [
                    'image/jpeg',
                    'image/png',
                ],
                'mimeTypesMessage' => 'Please upload a valid image'
            ])
        ];
        if (!$isEdit || !$user->getImageFilename()) {
            $imageConstraints[] = new NotNull([
                'message' => 'Please upload an image',
            ]);
        }
        // For the full reference of options defined by each form field type
        // see https://symfony.com/doc/current/reference/forms/types.html

        // By default, form fields include the 'required' attribute, which enables
        // the client-side form validation. This means that you can't test the
        // server-side validation errors from the browser. To temporarily disable
        // this validation, set the 'required' attribute to 'false':
        // $builder->add('title', null, ['required' => false, ...]);
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                //'disabled' => true,
            ])
            ->add('name', TextType::class, [
                'label' => 'Username',
                'required' => false,
            ])
            ->add('about', TextareaType::class, [
                'label' => 'About',
            ])
            ->add('imageFilename', FileType::class, [
                'mapped' => false,
                // make it optional so you don't have to re-upload the image file
                // everytime you edit the Profile details
                'required' => false,
                'constraints' => $imageConstraints
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
