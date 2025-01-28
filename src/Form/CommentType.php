<?php 
namespace App\Form;

use App\Entity\Comments;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                'attr' => ['placeholder' => 'Écrivez votre commentaire...', 'rows' => 3],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Publier le commentaire',
                'attr' => ['class' => 'absolute bg-pink-500 text-white font-semibold py-2 px-4 rounded']
            ])
            ->add('content', TextType::class, [
                'attr' => ['class' => 'relative bg-gray-200 placeholder-gray-500 text-black font-medium py-2 px-4 rounded', 'placeholder' => 'Écrivez votre commentaire...']
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Publier',
                'attr' => ['class' => 'hidden']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comments::class,
        ]);
    }
}