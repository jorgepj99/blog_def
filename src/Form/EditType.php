<?php
/**
 * Created by PhpStorm.
 * User: linux
 * Date: 05/02/19
 * Time: 17:25
 */

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //función para construir un formulario
        //añadimos add por tantos campos que tengamos en la clase User(en entity)
        $builder
            ->add('username', TextType::class, [
                'required' => 'required',
                'attr' => [
                    'class' => 'form-username form-control',
                    'placeholder' => 'Username'
                ]
            ])
            ->add('email', EmailType::class, [
                'required' => 'required',
                'attr' => [
                    'class' => 'form-email form-control',
                    'placeholder' => 'Email@email.com'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => 'App\Entity\User']);
    }
}