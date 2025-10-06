<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Administration manager for the Tag Bundle.
 */
class TagAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_tag';

    /**
     * @param RouteCollectionInterface $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('export')
            ->remove('edit')
            ->remove('delete');
    }

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form->add('id', TextType::class, ['label' => 'tag.form.id', 'attr' => ['readonly' => true]])
            ->add('name', TextType::class, ['label' => 'tag.form.name', 'required' => true])
            ->add('category', TextType::class, ['label' => 'tag.form.category', 'required' => false])
            ->add('isOfficial', CheckboxType::class, ['label' => 'tag.form.is_official', 'required' => false]);
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id', null, ['label' => 'tag.filter.id'])
            ->add('name', null, ['label' => 'tag.filter.name'])
            ->add('category', null, ['label' => 'tag.filter.category'])
            ->add('isOfficial', null, ['label' => 'tag.filter.is_official']);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('id', null, ['label' => 'tag.list.id'])
            ->add('name', 'text', ['label' => 'tag.list.name', 'editable' => true])
            ->add('category', 'text', ['label' => 'tag.list.category', 'editable' => true])
            ->add('isOfficial', 'boolean', ['label' => 'tag.list.is_official', 'editable' => true])
            ->add('_action', 'actions', ['actions' => ['show' => [], 'edit' => [], 'delete' => []]]);
    }

    /**
     * @param ShowMapper $show
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('id', null, ['label' => 'tag.show.id'])
            ->add('name', null, ['label' => 'tag.show.name'])
            ->add('category', null, ['label' => 'tag.show.category'])
            ->add('isOfficial', null, ['label' => 'tag.show.is_official']);
    }
}
