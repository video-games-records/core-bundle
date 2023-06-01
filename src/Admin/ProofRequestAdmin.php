<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\ChoiceFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ModelFilter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Intl\Locale;
use VideoGamesRecords\CoreBundle\Entity\ProofRequest;
use VideoGamesRecords\CoreBundle\ValueObject\ProofRequestStatus;

class ProofRequestAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_proofrequest';


    /** @var ContainerInterface */
    private ContainerInterface $container;


    public function setContainer(ContainerInterface $container)
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
    private function getLibChart(): string
    {
        $locale = Locale::getDefault();
        return ($locale == 'fr') ? 'libChartFr' : 'libChartEn';
    }

    /**
     * @param RouteCollectionInterface $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('create')
            ->remove('delete')
            ->remove('export');
    }


    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add(
                'playerRequesting',
                ModelListType::class,
                [
                    'data_class' => null,
                    'btn_add' => false,
                    'btn_list' => false,
                    'btn_edit' => false,
                    'btn_delete' => false,
                    'btn_catalogue' => false,
                    'label' => 'label.player.requesting',
                ]
            )
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
                'status',
                ChoiceType::class,
                [
                    'label' => 'label.status',
                    'choices' => ProofRequestStatus::getStatusChoices(),
                ]
            )
            ->add('message', CKEditorType::class, [
                'label' => 'label.message',
                'required' => true,
                'attr' => array(
                    'readonly' => true,
                ),
                'config' => array(
                    'height' => '100',
                    'toolbar' => 'standard'
                ),
            ])
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
            ->add('status', ChoiceFilter::class, [
                'label' => 'label.status',
                'field_type' => ChoiceType::class,
                'field_options' => [
                    'choices' => ProofRequestStatus::getStatusChoices(),
                    'multiple' => false,
                ]
            ])
            ->add('playerChart.player', ModelFilter::class, [
                'field_type' => ModelAutocompleteType::class,
                'field_options' => ['property'=>'pseudo'],
                'label' => 'label.player',
            ])
            ->add('playerRequesting', ModelFilter::class, [
                'field_type' => ModelAutocompleteType::class,
                'field_options' => ['property'=>'pseudo'],
                'label' => 'label.player.requesting',
            ])
            ->add('playerResponding', ModelFilter::class, [
                'field_type' => ModelAutocompleteType::class,
                'field_options' => ['property'=>'pseudo'],
                'label' => 'label.player.responding',
            ]);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id', null, ['label' => 'label.id'])
            ->add('createdAt', null, ['label' => 'label.createdAt'])
            ->add('playerRequesting', null, [
                'associated_property' => 'pseudo',
                'label' => 'label.player.requesting',
            ])
            ->add('playerResponding', null, [
                'associated_property' => 'pseudo',
                'label' => 'label.player.responding',
            ])
            ->add('playerChart.player', null, [
                'associated_property' =>  'pseudo',
                'label' => 'label.player',
                'sortable' => true,
                'sort_field_mapping' => array(
                    'fieldName' => 'pseudo'
                ),
                'sort_parent_association_mappings' => array(
                    array('fieldName' => 'playerChart'),
                    array('fieldName' => 'player'),
                )
            ])
            ->add('playerChart.chart.group.game', null, [
                'associated_property' =>  $this->getLibGame(),
                'label' => 'label.name',
                'sortable' => true,
                'sort_field_mapping' => array(
                    'fieldName' => $this->getLibGame()
                ),
                'sort_parent_association_mappings' => array(
                    array('fieldName' => 'playerChart'),
                    array('fieldName' => 'chart'),
                    array('fieldName' => 'group'),
                    array('fieldName' => 'game'),
                )
            ])
            ->add('playerChart.chart', null, [
                'associated_property' =>  $this->getLibChart(),
                'label' => 'label.chart',
                'sortable' => true,
                'sort_field_mapping' => array(
                    'fieldName' => $this->getLibChart()
                ),
                'sort_parent_association_mappings' => array(
                    array('fieldName' => 'playerChart'),
                    array('fieldName' => 'chart'),
                )
            ])
            ->add('message', 'text', [
                'label' => 'label.message',
                'header_style' => 'width: 30%'
            ])
            ->add(
                'status',
                'choice',
                [
                    'label' => 'label.status',
                    'editable' => true,
                    'choices' => ProofRequestStatus::getStatusChoices(),
                    'choice_translation_domain' => false,
                ]
            )
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'view_chart' => [
                        'template' => '@VideoGamesRecordsCore/Admin/view_chart_link.html.twig'
                    ],
                ],
                'header_style' => 'width: 220px'
            ]);
    }

    /**
     * @param ShowMapper $show
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id', null, ['label' => 'label.id'])
            ->add('createdAt', null, ['label' => 'label.createdAt'])
            ->add('playerRequesting', null, ['label' => 'label.player.requesting'])
            ->add('playerResponding', null, ['label' => 'label.player.responding'])
            ->add('playerChart', null, ['label' => 'label.playerChart'])
            ->add('message', null, ['label' => 'label.message'])
            ->add('status', null, ['label' => 'label.status']);
    }

    /**
     * @param $object
     */
    public function preValidate($object): void
    {
        $player = $this->getPlayer();

        if ($player) {
            if (($object->getPlayerRequesting()->getId() === $player->getId())
            || ($object->getPlayerChart()->getPlayer()->getId() === $player->getId())) {
                $this->container->get('session')->getFlashBag()->add(
                    'error',
                    "You can't update this request"
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
     * @throws ORMException
     */
    public function preUpdate($object): void
    {
        /** @var EntityManager $em */
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $originalObject = $em->getUnitOfWork()->getOriginalEntityData($object);

        // Cant change status final
        if (in_array($originalObject['status'], array(ProofRequestStatus::STATUS_ACCEPTED, ProofRequestStatus::STATUS_REFUSED), true)) {
            $object->setStatus($originalObject['status']);
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
