<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\DoctrineORMAdminBundle\Filter\ModelAutocompleteFilter;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class GroupAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_group';

    /**
     * @param RouteCollectionInterface $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('export')
            ->add('copy', $this->getRouterIdParameter().'/copy')
            ->add('copy-with-lib-chart', $this->getRouterIdParameter().'/copy-with-lib-chart');
    }

    /**
     * @param ProxyQueryInterface $query
     * @return ProxyQueryInterface
     */
    protected function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $query = parent::configureQuery($query);
        $query->innerJoin($query->getRootAliases()[0]  . '.translations', 't', 'WITH', "t.locale='en'")
            ->addSelect('t');
        return $query;
    }

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $gameOptions = [];
        if (($this->hasRequest()) && ($this->isCurrentRoute('create'))) {
            $idGame = $this->getRequest()->get('idGame', null);
            if ($idGame !== null) {
                $this->getRequest()->getSession()->set('vgrcorebundle_admin_group.idGame', $idGame);
            }

            if ($this->getRequest()->getSession()->has('vgrcorebundle_admin_group.idGame')) {
                $idGame= $this->getRequest()->getSession()->get('vgrcorebundle_admin_group.idGame');
                $entityManager = $this->getModelManager()
                    ->getEntityManager('VideoGamesRecords\CoreBundle\Entity\Game');
                $game = $entityManager->getReference('VideoGamesRecords\CoreBundle\Entity\Game', $idGame);
                $gameOptions = ['data' => $game];
            }
        }

        $form
            ->add('id', TextType::class, [
                'label' => 'idGroup',
                'attr' => [
                    'readonly' => true,
                ]
            ])
            ->add('game', ModelListType::class, array_merge(
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
            ->add('boolDLC', CheckboxType::class, [
                'label' => 'DLC ?',
                'required' => false,
            ])
            ->add('translations', TranslationsType::class, [
                'required' => true,
            ]);
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id')
            ->add('translations.name', null, ['label' => 'Name'])
            ->add('game', ModelAutocompleteFilter::class, [], null, [
                'property' => 'translations.name',
            ]);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id')
            ->add(
                'translations',
                null,
                [
                    'associated_property' => 'name',
                    'label' => 'Name'
                ]
            )
            ->add('slug', null, ['label' => 'Slug'])
            ->add('game', null, [
                'associated_property' => 'defaultName',
                'label' => 'Game',
            ])
            ->add('boolDLC', 'boolean')
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'copy' => [
                        'template' => 'VideoGamesRecordsCoreBundle:Admin:group_copy_link.html.twig'
                    ],
                    'copy2' => [
                        'template' => 'VideoGamesRecordsCoreBundle:Admin:group_copy2_link.html.twig'
                    ],
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
     * @param ShowMapper $show
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('getDefaultName', null, ['label' => 'Name'])
            ->add('game', null, [
                'associated_property' => 'defaultName',
                'label' => 'Game',
            ])
            ->add('charts');
    }
}
