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
        // For the full reference of options defined by each form field type
        // see https://symfony.com/doc/current/reference/forms/types.html

        // By default, form fields include the 'required' attribute, which enables
        // the client-side form validation. This means that you can't test the
        // server-side validation errors from the browser. To temporarily disable
        // this validation, set the 'required' attribute to 'false':
        // $builder->add('title', null, ['required' => false, ...]);
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
//        if (!$isEdit || !$user->getImageFile()) {
//            $imageConstraints[] = new NotNull([
//                'message' => 'Please upload an image',
//            ]);
//        }
        $builder
            ->add('email', EmailType::class, [
                'label' => 'label.email',
                //'disabled' => true,
            ])
            ->add('name', TextType::class, [
                'label' => 'label.username',
                'required' => false,
            ])
            ->add('about', TextareaType::class, [
                'label' => 'label.about',
            ])
            ->add('imageFile', FileType::class, [
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
