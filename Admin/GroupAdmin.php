<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class GroupAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_group';

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
        $gameOptions = [];
        if (($this->hasRequest()) && ($this->isCurrentRoute('create'))) {
            $idGame = $this->getRequest()->get('idGame', null);
            if ($idGame !== null) {
                $this->getRequest()->getSession()->set('vgrcorebundle_admin_group.idGame', $idGame);
            }

            if ($this->getRequest()->getSession()->has('vgrcorebundle_admin_group.idGame')) {
                $idGame = $this->getRequest()->getSession()->get('vgrcorebundle_admin_group.idGame');
                $entityManager = $this->getModelManager()
                    ->getEntityManager('VideoGamesRecords\CoreBundle\Entity\Game');
                $game = $entityManager->getReference('VideoGamesRecords\CoreBundle\Entity\Game', $idGame);
                $gameOptions = ['data' => $game];
            }
        }

        $formMapper
            ->add('id', 'text', [
                'label' => 'idGroup',
                'attr' => [
                    'readonly' => true,
                ]
            ])
            ->add('game', 'sonata_type_model_list', array_merge(
                $gameOptions,
                [
                    'data_class' => null,
                    'btn_add' => false,
                    'btn_list' => true,
                    'btn_edit' => false,
                    'btn_delete' => false,
                    'btn_catalogue' => true,
                    'label' => 'Game',
                ]
            ))
            ->add('boolDLC', 'checkbox', [
                'label' => 'DLC ?',
                'required' => false,
            ])
            ->add('translations', TranslationsType::class, [
                'required' => true,
            ]);
    }

    /**
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('translations.name')
            ->add('game', 'doctrine_orm_model_autocomplete', [], null, [
                'property' => 'translations.name',
            ]);
    }

    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('getDefaultName', null, ['label' => 'Name'])
            ->add('game', null, [
                'associated_property' => 'defaultName',
                'label' => 'Game',
            ])
            ->add('boolDLC', 'boolean')
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'groups' => [
                        'template' => 'VideoGamesRecordsCoreBundle:Admin:group_charts_link.html.twig'
                    ],
                    'add_chart' => [
                        'template' => 'VideoGamesRecordsCoreBundle:Admin:group_add_chart_link.html.twig'
                    ],
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
            ->add('getDefaultName', null, ['label' => 'Name'])
            ->add('game', null, [
                'associated_property' => 'defaultName',
                'label' => 'Game',
            ])
            ->add('charts');
    }
}
