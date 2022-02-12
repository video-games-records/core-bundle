<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use VideoGamesRecords\CoreBundle\Entity\Badge;

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
        $form->add('id', TextType::class, ['label' => 'label.id', 'attr' => ['readonly' => true]])
            ->add(
                'type',
                ChoiceType::class,
                [
                    'label' => 'label.type',
                    'choices' => Badge::getTypeChoices(),
                ]
            )
            ->add('picture', TextType::class, ['label' => 'label.picture'])
            ->add('value', TextType::class, ['label' => 'label.Value', 'required' => false]);
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id', null, ['label' => 'label.id'])
            ->add('type', null, ['label' => 'label.type'])
            ->add('picture', null, ['label' => 'label.picture'])
            ->add('game.id', null, ['label' => 'label.game.id'])
            ->add('game.libGameFr', null, ['label' => 'label.game.fr'])
            ->add('game.libGameEn', null, ['label' => 'label.game.en']);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('id', null, ['label' => 'label.id'])
            ->add('type', null, ['label' => 'label.type'])
            ->add('picture', 'text', ['label' => 'label.picture', 'editable' => true])
            ->add('value', null, ['label' => 'label.value'])
            ->add('_action', 'actions', ['actions' => ['show' => [], 'edit' => []]]);
    }

    /**
     * @param ShowMapper $show
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('id',null, ['label' => 'label.id'])
            ->add('type',null, ['label' => 'label.type'])
            ->add('picture',null, ['label' => 'label.picture'])
            ->add('value', null, ['label' => 'label.value']);
    }
}
