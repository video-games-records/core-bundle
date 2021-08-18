<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sonata\AdminBundle\Form\Type\ModelListType;


class RuleAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_rule';

    /**
     * @param RouteCollectionInterface $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('export');
    }

    /**
     * @param ProxyQueryInterface $query
     * @return ProxyQueryInterface
     */
    protected function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $query = parent::configureQuery($query);
        $query->leftJoin($query->getRootAliases()[0]  . '.translations', 't')
            ->addSelect('t');
        return $query;
    }

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('name', TextType::class, [
                'label' => 'Name',
                'required' => true,
            ])
             ->add(
                'player',
                ModelListType::class,
                [
                    'data_class' => null,
                    'btn_add' => false,
                    'btn_list' => true,
                    'btn_edit' => false,
                    'btn_delete' => false,
                    'btn_catalogue' => true,
                    'label' => 'Player',
                ]
            )
            ->add('translations', TranslationsType::class, [
                'fields' => [
                    'text' => [
                        'field_type' => CKEditorType::class,
                        'label' => ' Rules',
                        'required' => false,
                        'locale_options' => [
                            'en' => [
                                'config' => array(
                                    'height' => '200',
                                    'toolbar' => 'standard'
                                ),
                            ],
                            'fr' => [
                                'config' => array(
                                    'height' => '200',
                                    'toolbar' => 'standard'
                                ),
                            ],
                        ]
                    ]
                ]
            ]);
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id')
            ->add('name');
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id')
            ->add('name', null, ['label' => 'Name'])
             ->add('player', null, [
                'associated_property' => 'pseudo',
                'label' => 'Player',
            ])
            ->add('created_at', 'datetime', ['label' => 'Created At'])
            ->add('updated_at', 'datetime', ['label' => 'Updated At'])
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                ]
            ]);
    }

    /**
     * @param ShowMapper $show
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('name', null, ['label' => 'Name'])
            ->add('player')
            ->add('created_at', 'datetime', ['label' => 'Created At'])
            ->add('updated_at', 'datetime', ['label' => 'Updated At'])
            ->add('games')
            ->add('getDefaultText', null, ['label' => 'Text', 'safe' => true]);
    }
}
