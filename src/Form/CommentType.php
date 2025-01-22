<?php 
namespace App\Form;

use App\Entity\Comments;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', TextareaType::class, [
                'label' => 'Ajouter un commentaire',
                'attr' => ['placeholder' => 'Écrivez votre commentaire...', 'rows' => 3],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Publier le commentaire',
                'attr' => ['class' => 'bg-pink-500 text-white font-semibold py-2 px-4 rounded']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comments::class,
        ]);
    }
}