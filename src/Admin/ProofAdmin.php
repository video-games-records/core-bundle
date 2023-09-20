<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use Doctrine\ORM\EntityManager;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\ChoiceFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ModelFilter;
use Sonata\DoctrineORMAdminBundle\Filter\NullFilter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Intl\Locale;
use VideoGamesRecords\CoreBundle\ValueObject\ProofStatus;

class ProofAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_proof';

    /** @var ContainerInterface */
    private ContainerInterface $container;

    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

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
     * @return string
     */
    private function getLibChart(): string
    {
        $locale = Locale::getDefault();
        return ($locale == 'fr') ? 'libChartFr' : 'libChartEn';
    }

    /**
     * @param ProxyQueryInterface $query
     * @return ProxyQueryInterface
     */
    protected function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $query = parent::configureQuery($query);
        $query->innerJoin($query->getRootAliases()[0] . '.chart', 'chr')
            ->addSelect('chr')
            ->innerJoin('chr.group', 'grp')
            ->addSelect('grp')
            ->innerJoin('grp.game', 'gam')
            ->addSelect('gam');
        return $query;
    }

    /**
     * @param RouteCollectionInterface $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('create')
            ->remove('delete')
            ->remove('export')
            ->add('stats', 'stats');
    }


    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add(
                'playerResponding',
                ModelListType::class,
                [
                    'data_class' => null,
                    'btn_add' => false,
                    'btn_list' => false,
                    'btn_edit' => false,
                    'btn_delete' => false,
                    'btn_catalogue' => false,
                    'label' => 'label.player.responding',
                ]
            )
            ->add(
                'playerChart',
                ModelListType::class,
                [
                    'data_class' => null,
                    'btn_add' => false,
                    'btn_list' => false,
                    'btn_edit' => false,
                    'btn_delete' => false,
                    'btn_catalogue' => false,
                    'label' => 'label.playerChart.edit',
                ]
            )
            ->add(
                'status',
                ChoiceType::class,
                [
                    'label' => 'label.status',
                    'choices' => ProofStatus::getStatusChoices(),
                    'choice_translation_domain' => false,
                ]
            )
            ->add('response', CKEditorType::class, [
                'label' => 'label.proof.response',
                'required' => false,
                'config' => array(
                    'height' => '100',
                    'toolbar' => 'standard'
                ),
            ]);
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id', null, ['label' => 'label.id'])
            ->add('player', ModelFilter::class, [
                'field_type' => ModelAutocompleteType::class,
                'field_options' => ['property' => 'pseudo'],
                'label' => 'label.player'
            ])
            ->add('player.pseudo', null, ['label' => 'label.pseudo'])
            ->add('chart.group.game', ModelFilter::class, [
                'field_type' => ModelAutocompleteType::class,
                'field_options' => ['property' => $this->getLibGame()],
                'label' => 'label.game'
            ])
            ->add('chart.group.game.libGameEn', null, ['label' => 'label.game.en'])
            ->add('chart.group.game.libGameFr', null, ['label' => 'label.game.fr'])
            ->add('status', ChoiceFilter::class, [
                'label' => 'label.proof.status',
                'field_type' => ChoiceType::class,
                'field_options' => [
                    'choices' => ProofStatus::getStatusChoices(),
                    'multiple' => false,
                ]
            ])
            ->add('playerChart.status', null, ['label' => 'label.playerChart.status'])
            ->add('playerResponding', ModelFilter::class, [
                'field_type' => ModelAutocompleteType::class,
                'field_options' => ['property' => 'pseudo'],
                'label' => 'label.player.responding'
            ])
            ->add('video', NullFilter::class, ['label' => 'label.video_is_null'])
            ->add('picture', NullFilter::class, ['label' => 'label.picture_is_null'])
        ;
    }


    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id', null, ['label' => 'label.id'])
            ->add('player', null, [
                'associated_property' => 'pseudo',
                'label' => 'label.player',
            ])
            ->add('chart.group.game', null, [
                'associated_property' =>  $this->getLibGame(),
                'label' => 'label.game',
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
                'label' => 'label.group',
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
                'associated_property' => $this->getLibChart(),
                'label' => 'label.chart',
            ])
            ->addIdentifier('type', null, ['label' => 'label.type'])
            ->add('playerResponding', null, [
                'associated_property' => 'pseudo',
                'label' => 'label.player.responding',
            ])
            ->add(
                'status',
                'choice',
                [
                    'label' => 'label.status',
                    'editable' => true,
                    'choices' => ProofStatus::getStatusChoices(),
                    'choice_translation_domain' => false,
                ]
            )
            ->add('playerChart.status', null, ['label' => 'label.playerChart.status'])
            ->add('created_at', 'datetime', ['label' => 'label.createdAt'])
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
            ->add('id', null, ['label' => 'label.id'])
            ->add('Player', null, ['label' => 'label.player'])
            ->add('chart.group.game', null, array(
                'associated_property' => $this->getLibGame(),
                'label' => 'label.game',
            ))
            ->add('chart.group', null, array(
                'associated_property' => $this->getLibGroup(),
                'label' => 'label.group',
            ))
            ->add('chart', null, array(
                'associated_property' => $this->getLibChart(),
                'label' => 'label.chart',
            ))
            ->add('created_at', 'datetime', ['label' => 'label.createdAt'])
            ->add('updated_at', 'datetime', ['label' => 'label.updatedAt'])
            ->add('checkedAt', 'datetime', ['label' => 'label.checkedAt'])
            ->add('playerChart', null, ['label' => 'label.score'])
            ->add('picture', null, ['label' => 'label.picture'])
            ->add('video', null, ['label' => 'label.video'])
            ->add('playerResponding', null, ['label' => 'label.player.responding'])
            ->add('status', null, ['label' => 'label.status'])
            ->add('proofRequest.message', null, ['label' => 'label.message']);
    }

    /**
     * @param $object
     */
    public function preValidate($object): void
    {
        $player = $this->getPlayer();

        if ($object->getPlayerChart() != null) {
            if ($object->getPlayerChart()->getPlayer()->getId() === $player->getId()) {
                $this->container->get('session')->getFlashBag()->add(
                    'error',
                    "You can't update this proof"
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

    /**
     * @param $object
     * @return void
     */
    public function preUpdate($object): void
    {
        /** @var EntityManager $em */
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $originalObject = $em->getUnitOfWork()->getOriginalEntityData($object);

        // Cant change status final (CLOSED & REFUSED)
        if (in_array($originalObject['status'], array(ProofStatus::STATUS_CLOSED, ProofStatus::STATUS_REFUSED), true)) {
            $object->setStatus($originalObject['status']);
        }


        if ($object->getPlayerChart() == null) {
            $object->setStatus(ProofStatus::STATUS_CLOSED);
        }
    }

    /**
     * @return mixed
     */
    private function getPlayer()
    {
        /** @var EntityManager $em */
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        return $em->getRepository('VideoGamesRecords\CoreBundle\Entity\Player')->getPlayerFromUser($user);
    }
}
