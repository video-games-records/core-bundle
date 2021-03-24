<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use VideoGamesRecords\CoreBundle\Entity\Video;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\DoctrineORMAdminBundle\Filter\ModelAutocompleteFilter;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Sonata\AdminBundle\Form\Type\ModelListType;

class VideoAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_video';

    /**
     * @param RouteCollectionInterface $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('export');
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('id', TextType::class, [
                'label' => 'idVideo',
                'required' => false,
                'attr' => array(
                    'readonly' => true,
                )
            ])
            ->add('player', ModelListType::class, [
                'data_class' => null,
                'btn_add' => false,
                'btn_list' => true,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => true,
                'label' => 'Player',
            ])
            ->add('game', ModelListType::class, [
                'data_class' => null,
                'btn_add' => false,
                'btn_list' => true,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => true,
                'label' => 'Game',
            ])
            ->add('libVideo', TextType::class, [
                'label' => 'libVideo',
                'required' => true,
            ])
            ->add(
                'type',
                ChoiceType::class,
                [
                    'label' => 'Type',
                    'choices' => Video::getTypeChoices(),
                ]
            )
            ->add('videoId', TextType::class, [
                'label' => 'VideoId',
                'required' => true,
            ])
            ->add('url', TextType::class, [
                'label' => 'Url',
                'required' => true,
            ])
            ->add('boolActive', CheckboxType::class, [
                'label' => 'Active ?',
                'required' => false,
            ]);
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id')
            ->add('boolActive')
            ->add('type')
            ->add('player', ModelAutocompleteFilter::class, [], null, [
                'property' => 'pseudo',
            ]);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id')
            ->add('player', null, [
                'associated_property' => 'pseudo',
                'label' => 'Player',
            ])
            ->add('game', null, [
                'associated_property' => 'defaultName',
                'label' => 'Game',
            ])
            ->add(
                'libVideo'
            )
            ->add(
                'type'
            )
            ->add(
                'videoId'
            )
            ->add(
                'url'
            )
            ->add(
                'boolActive',
                'boolean',
                [
                    'label' => 'Active ?',
                    'editable' => true,
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
    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('boolActive')
            ->add('libVideo')
            ->add('player')
            ->add('game')
            ->add('url');
    }
}
