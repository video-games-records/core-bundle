<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use VideoGamesRecords\CoreBundle\Entity\Video;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\DoctrineORMAdminBundle\Filter\ModelAutocompleteFilter;

class VideoAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_video';

    /**
     * @inheritdoc
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('export');
    }

    /**
     * @inheritdoc
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('id', 'text', [
                'label' => 'idVideo',
                'required' => false,
                'attr' => array(
                    'readonly' => true,
                )
            ])
            ->add('player', 'sonata_type_model_list', [
                'data_class' => null,
                'btn_add' => false,
                'btn_list' => true,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => true,
                'label' => 'Player',
            ])
            ->add('game', 'sonata_type_model_list', [
                'data_class' => null,
                'btn_add' => false,
                'btn_list' => true,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => true,
                'label' => 'Game',
            ])
            ->add('libVideo', 'text', [
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
            ->add('url', 'text', [
                'label' => 'Url',
                'required' => true,
            ])
            ->add(
                'status',
                ChoiceType::class,
                [
                    'label' => 'Status',
                    'choices' => Video::getStatusChoices(),
                ]
            );
    }

    /**
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('status')
            ->add('player', ModelAutocompleteFilter::class, [], null, [
                'property' => 'pseudo',
            ]);
    }


    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $listMapper)
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
                'status',
                'choice',
                [
                    'label' => 'Status',
                    'editable' => true,
                    'choices' => Video::getStatusChoices(),
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
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('status')
            ->add('libVideo')
            ->add('player')
            ->add('game')
            ->add('url');
    }
}
