<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use VideoGamesRecords\CoreBundle\Entity\Badge;
use VideoGamesRecords\CoreBundle\Enum\BadgeType;

/**
 * Administration manager for the Badge Bundle.
 */
class BadgeAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_badge';

    /**
     * @param RouteCollectionInterface $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->remove('export');
    }

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form->add('id', TextType::class, ['label' => 'badge.form.id', 'attr' => ['readonly' => true]])
            ->add(
                'type',
                ChoiceType::class,
                [
                    'label' => 'badge.form.type',
                    'choices' => array_combine(
                        array_map(fn(BadgeType $case) => $case->value, BadgeType::cases()),
                        BadgeType::cases()
                    ),
                ]
            )
            ->add('picture', TextType::class, ['label' => 'badge.form.picture'])
            ->add('value', TextType::class, ['label' => 'badge.form.value', 'required' => false]);
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id', null, ['label' => 'badge.filter.id'])
            ->add('type', null, ['label' => 'badge.filter.type'])
            ->add('picture', null, ['label' => 'badge.filter.picture'])
            ->add('game.id', null, ['label' => 'badge.filter.game.id'])
            ->add('game.libGameFr', null, ['label' => 'badge.filter.game.fr'])
            ->add('game.libGameEn', null, ['label' => 'badge.filter.game.en']);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('id', null, ['label' => 'badge.list.id'])
            ->add('type', null, ['label' => 'badge.list.type'])
            ->add('picture', 'text', ['label' => 'badge.list.picture', 'editable' => true])
            ->add('value', null, ['label' => 'badge.list.value'])
            ->add('_action', 'actions', ['actions' => ['show' => [], 'edit' => []]]);
    }

    /**
     * @param ShowMapper $show
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('id', null, ['label' => 'badge.show.id'])
            ->add('type', null, ['label' => 'badge.show.type'])
            ->add('picture', null, ['label' => 'badge.show.picture'])
            ->add('value', null, ['label' => 'badge.show.value']);
    }
}
