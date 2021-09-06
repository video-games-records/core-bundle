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
    protected $baseRouteName = 'pnbadgebundle_admin_badge';

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
        $form->add('id', TextType::class, ['label' => 'idBadge', 'attr' => ['readonly' => true]])
            ->add(
                'type',
                ChoiceType::class,
                [
                    'label' => 'Type',
                    'choices' => Badge::getTypeChoices(),
                ]
            )
            ->add('picture', TextType::class, ['label' => 'label.picture'])
            ->add('value', TextType::class, ['label' => 'Value', 'required' => false]);
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('type', null, ['label' => 'label.type'])
            ->add('picture', null, ['label' => 'label.picture']);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('id')
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
        $show->add('id')
            ->add('type',null, ['label' => 'label.type'])
            ->add('picture',null, ['label' => 'label.picture'])
            ->add('value', null, ['label' => 'label.value']);
    }
}
