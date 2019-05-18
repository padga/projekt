<?php
/**
 * Transaction Type.
 */

namespace App\Form;

use App\Entity\Tag;
use App\Entity\Transaction;
use App\Entity\Type;
use App\Entity\User;
use App\Repository\TagRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TransactionType.
 */
class TransactionType extends AbstractType
{
    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @see FormTypeExtensionInterface::buildForm()
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'amount',
            IntegerType::class,
            [
                'label' => 'label.amount',
                'required' => true,
            ]
        );
        $builder->add(
            'type',
            EntityType::class,
            [
                'label' => 'label.type',
                'class' => Type::class,
                'choice_label' => 'typeTitle',
                    'required' => true,
            ]
        );
        $user = $options['user'];
        $builder->add(
            'tag',
            EntityType::class,
            [
                'label' => 'label.tags',
                'class' => Tag::class,
                'query_builder' => function (TagRepository $repository) use ($user) {
                    return $repository->queryByAuthor($user);
                },
                'choice_label' => 'tagName',
                'required' => true,
                'expanded' => true,
                'multiple' => true,
            ]
        );
//        $user = $options['user'];
//        $builder->add(
//            'tags',
//            EntityType::class,
//            [
//                'class' => Tag::class,
//                'choice_label' => function (TagRepository $repository) use ($user) {
//                    return $repository->queryByAuthor($user);
//                },
//                'label' => 'label.tags',
//                'placeholder' => 'label.none',
//                'required' => false,
//                'expanded' => true,
//                'multiple' => true,
//            ]
//        );
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Transaction::class]);
        $resolver->setRequired('user');
        $resolver->setAllowedTypes('user', array(User::class, 'int'));
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefix defaults to the underscored short class name with
     * the "Type" suffix removed (e.g. "UserProfileType" => "user_profile").
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix(): string
    {
        return 'transaction';
    }
}
