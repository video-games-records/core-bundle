<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\ModelFilter;
use Sonata\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Intl\Locale;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use VideoGamesRecords\CoreBundle\Event\Admin\AdminPlayerChartUpdated;
use VideoGamesRecords\CoreBundle\Traits\Accessor\SetEventDispacther;
use VideoGamesRecords\CoreBundle\Traits\Accessor\SetRequestStack;

class PlayerChartAdmin extends AbstractAdmin
{
    use SetRequestStack;
    use SetEventDispacther;

    protected $baseRouteName = 'vgrcorebundle_admin_player_chart';


    /**
     * @return string
     */
    private function getLibGame(): string
    {
        $locale = Locale::getDefault();
        return ($locale == 'fr') ? 'libGameFr' : 'libGameEn';
    }

    /**
     * @return string
     */
    private function getLibGroup(): string
    {
        $locale = Locale::getDefault();
        return ($locale == 'fr') ? 'libGroupFr' : 'libGroupEn';
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('create')
            ->remove('export');
    }

    /**
     * @param ProxyQueryInterface $query
     * @return ProxyQueryInterface
     */
    protected function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $query = parent::configureQuery($query);

        $rootAlias = current($query->getRootAliases());
        $query
            ->innerJoin($rootAlias[0] . '.chart', 'chart')
            ->addSelect('chart')
            ->innerJoin($rootAlias[0] . '.player', 'player')
            ->addSelect('player')
            ->innerJoin('chart.group', 'grp')
            ->addSelect('grp')
            ->innerJoin('grp.game', 'game')
            ->addSelect('game');

        return $query;
    }


    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('id', TextType::class, [
                'label' => 'player_chart.form.id',
                'attr' => [
                    'readonly' => true,
                ]
            ])
            ->add('player', ModelListType::class, [
                'btn_add' => false,
                'btn_list' => false,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => false,
                'label' => 'player_chart.form.player',
            ])
            ->add('chart', ModelListType::class, [
                'btn_add' => false,
                'btn_list' => true,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => true,
                'label' => 'player_chart.form.chart',
            ])
            ->add('platform', null, ['label' => 'player_chart.form.platform'])
            ->add('status', null, ['label' => 'player_chart.form.status'])
            ->add('proof', ModelListType::class, [
                'btn_add' => false,
                'btn_list' => true,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => true,
                'label' => 'player_chart.form.proof',
            ])
            ->add('libs', CollectionType::class, array(
                'label' => 'player_chart.form.libs',
                'btn_add' => null,
                'by_reference' => false,
                'type_options' => array(
                    'delete' => false,
                )
            ), array(
                'edit' => 'inline',
                'inline' => 'table',
            ));
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id', null, ['label' => 'player_chart.filter.id'])
            ->add('status', null, ['label' => 'player_chart.filter.status'])
            ->add(
                'player',
                ModelFilter::class,
                [
                    'label' => 'player_chart.filter.player',
                    'field_type' => ModelAutocompleteType::class,
                    'field_options' => ['property' => 'pseudo'],
                ]
            )
            ->add(
                'chart.group.game',
                ModelFilter::class,
                [
                    'label' => 'player_chart.filter.game',
                    'field_type' => ModelAutocompleteType::class,
                    'field_options' => ['property' => 'libGameEn'],
                ]
            )
            ->add(
                'chart.group',
                ModelFilter::class,
                [
                    'label' => 'player_chart.filter.group',
                    'field_type' => ModelAutocompleteType::class,
                    'field_options' => ['property' => 'libGroupEn'],
                ]
            )
            ->add('chart.id', null, ['label' => 'player_chart.filter.chartId'])
            ->add('chart.libChartEn', null, ['label' => 'player_chart.filter.nameEn'])
            ->add('chart.libChartFr', null, ['label' => 'player_chart.filter.nameFr']);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id', null, ['label' => 'player_chart.list.id'])
            ->add('player', null, [
                'associated_property' => 'pseudo',
                'label' => 'player_chart.list.player',
            ])
            ->add('chart.group.game', null, [
                'associated_property' =>  $this->getLibGame(),
                'label' => 'player_chart.list.game',
                'sortable' => true,
                'sort_field_mapping' => array(
                    'fieldName' => $this->getLibGame()
                ),
                'sort_parent_association_mappings' => array(
                    array('fieldName' => 'chart'),
                    array('fieldName' => 'group'),
                    array('fieldName' => 'game'),
                )
            ])
            ->add('chart.group', null, [
                'associated_property' =>  $this->getLibGroup(),
                'label' => 'player_chart.list.group',
                'sortable' => true,
                'sort_field_mapping' => array(
                    'fieldName' => $this->getLibGroup()
                ),
                'sort_parent_association_mappings' => array(
                    array('fieldName' => 'chart'),
                    array('fieldName' => 'group')
                )
            ])
            ->add('chart', null, [
                'associated_property' => 'libChartEn',
                'label' => 'player_chart.list.chart',
            ])
            ->add('status', null, ['label' => 'player_chart.list.status'])
            ->add('libs', null, ['label' => 'player_chart.list.libs'])
            ->add('_action', 'actions', [
                'actions' => [
                    'edit' => [],
                    'show' => [],
                ]
            ]);
    }

    /**
     * @param ShowMapper $show
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id', null, ['label' => 'player_chart.show.id'])
            ->add('player', null, ['label' => 'player_chart.show.player'])
            ->add('chart', null, ['label' => 'player_chart.show.chart'])
            ->add('status', null, ['label' => 'player_chart.show.status'])
            ->add('lastUpdate', null, ['label' => 'player_chart.show.lastUpdate'])
            ->add('dateInvestigation', null, ['label' => 'player_chart.show.dateInvestigation'])
            ->add('proof', null, ['label' => 'player_chart.show.proof'])
            ->add('libs', null, ['label' => 'player_chart.show.libs']);
    }

    /**
     * @param $object
     */
    public function preValidate($object): void
    {
        $platform = $object->getPlatform();
        $platforms = $object->getChart()->getGroup()->getGame()->getPlatforms();

        if ($platform !== null) {
            $isPlatFormValid = false;
            foreach ($platforms as $row) {
                if ($platform === $row) {
                    $isPlatFormValid = true;
                }
            }
            if (!$isPlatFormValid) {
                $this->requestStack->getSession()->getFlashBag()->add(
                    'error',
                    "Platform is invalid"
                );
                $response = new RedirectResponse(
                    $this->generateUrl(
                        'edit',
                        array(
                            'id' => $object->getId()
                        )
                    )
                );
                header('Location: ' . $response->getTargetUrl());
                exit;
            }
        }
    }


    public function postUpdate(object $object): void
    {
        /** @var PlayerChart $object */
        parent::postUpdate($object);

        $this->eventDispatcher->dispatch(new AdminPlayerChartUpdated($object));
    }
}
