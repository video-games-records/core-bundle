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

class PlatformAdmin extends AbstractAdmin
{
    /**
     * @param RouteCollectionInterface $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection) : void
    {
        $collection
            ->remove('export');
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper) : void
    {
        $formMapper
            ->add('id', TextType::class, [
                'label' => 'id',
                'attr' => [
                    'readonly' => true,
                ]
            ])
            ->add('libPlatform', TextType::class, [
                'label' => 'Name',
                'required' => true,
            ])
            ->add('picture', TextType::class, [
                'label' => 'Picture',
                'required' => true,
            ])
            ->add(
                'status',
                ChoiceType::class,
                [
                    'label' => 'Status',
                    'choices' => [
                        'ACTIF' => 'ACTIF',
                        'INACTIF' => 'INACTIF',
                    ]
                ]
            );
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper) : void
    {
        $datagridMapper
            ->add('libPlatform');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper) : void
    {
        $listMapper
            ->addIdentifier('id')
            ->add('libPlatform', null, ['editable' => false])
            ->add('slug')
            ->add(
                'picture',
                'text',
                [
                    'label' => 'Picture',
                    'editable' => true
                ]
            )
            ->add(
                'status',
                'choice',
                [
                    'label' => 'Status',
                    'editable' => false,
                    'choices' => [
                        'ACTIF' => 'ACTIF',
                        'INACTIF' => 'INACTIF',
                    ]
                ]
            )
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                ]
            ]);
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper) : void
    {
        $showMapper
            ->add('idPlatform')
            ->add('libPlatform')
            ->add('picture')
            ->add('status')
            ->add('class');
    }
}
