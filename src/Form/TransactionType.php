<?php
/**
 * Transaction Type.
 */

namespace App\Form;

use App\Entity\Tag;
use App\Entity\Transaction;
use App\Entity\Type;
use App\Repository\TagRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TransactionType
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
                'class' => Type::class,
                'choice_label' => 'typeTitle',
                    'required' => true,
            ]
        );

        $builder->add(
            'tag',
            EntityType::class,
            [
                'class' => Tag::class,
                'choice_label' => 'tagName',
                'required' => true,
            ]
        );
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Transaction::class]);
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
