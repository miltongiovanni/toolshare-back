<?php

namespace App\Form;

use App\Entity\Profile;
use App\Entity\UserAdmin;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles')
            ->add('password')
            ->add('first_name')
            ->add('last_name')
            ->add('passcode')
            ->add('is_verified')
            ->add('created_at', null, [
                'widget' => 'single_text',
            ])
            ->add('slug')
            ->add('is_active')
            ->add('profile', EntityType::class, [
                'class' => Profile::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserAdmin::class,
        ]);
    }
}
