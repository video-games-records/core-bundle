<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Admin;

use Doctrine\ORM\EntityManager;
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
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Intl\Locale;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Form\Type\RichTextEditorType;
use VideoGamesRecords\CoreBundle\Traits\Accessor\SetRequestStack;
use VideoGamesRecords\CoreBundle\ValueObject\ProofRequestStatus;

class ProofRequestAdmin extends AbstractAdmin
{
    use SetRequestStack;

    protected $baseRouteName = 'vgrcorebundle_admin_proofrequest';

    private Security $security;

    public function setSecurity(Security $security): void
    {
        $this->security = $security;
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
                    'label' => 'proof_request.form.playerRequesting',
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
                    'label' => 'proof_request.form.playerResponding',
                ]
            )
            ->add(
                'status',
                ChoiceType::class,
                [
                    'label' => 'proof_request.form.status',
                    'choices' => ProofRequestStatus::getStatusChoices(),
                ]
            )
            ->add('message', RichTextEditorType::class, [
                'label' => 'proof_request.form.message',
                'required' => true,
                'attr' => array(
                    'readonly' => true,
                ),
            ])
            ->add('response', RichTextEditorType::class, [
                'label' => 'proof_request.form.response',
                'required' => false,
            ]);
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id', null, ['label' => 'proof_request.filter.id'])
            ->add('status', ChoiceFilter::class, [
                'label' => 'proof_request.filter.status',
                'field_type' => ChoiceType::class,
                'field_options' => [
                    'choices' => ProofRequestStatus::getStatusChoices(),
                    'multiple' => false,
                ]
            ])
            ->add('playerChart.player', ModelFilter::class, [
                'field_type' => ModelAutocompleteType::class,
                'field_options' => ['property' => 'pseudo'],
                'label' => 'proof_request.filter.player',
            ])
            ->add('playerRequesting', ModelFilter::class, [
                'field_type' => ModelAutocompleteType::class,
                'field_options' => ['property' => 'pseudo'],
                'label' => 'proof_request.filter.playerRequesting',
            ])
            ->add('playerResponding', ModelFilter::class, [
                'field_type' => ModelAutocompleteType::class,
                'field_options' => ['property' => 'pseudo'],
                'label' => 'proof_request.filter.playerResponding',
            ]);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id', null, ['label' => 'proof_request.list.id'])
            ->add('createdAt', null, ['label' => 'proof_request.list.createdAt'])
            ->add('playerRequesting', null, [
                'associated_property' => 'pseudo',
                'label' => 'proof_request.list.playerRequesting',
            ])
            ->add('playerResponding', null, [
                'associated_property' => 'pseudo',
                'label' => 'proof_request.list.playerResponding',
            ])
            ->add('playerChart.player', null, [
                'associated_property' =>  'pseudo',
                'label' => 'proof_request.list.player',
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
                'label' => 'proof_request.list.game',
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
                'label' => 'proof_request.list.chart',
                'sortable' => true,
                'sort_field_mapping' => array(
                    'fieldName' => $this->getLibChart()
                ),
                'sort_parent_association_mappings' => array(
                    array('fieldName' => 'playerChart'),
                    array('fieldName' => 'chart'),
                )
            ])
            ->add(
                'status',
                'choice',
                [
                    'label' => 'proof_request.list.status',
                    'editable' => true,
                    'choices' => ProofRequestStatus::getStatusChoices(),
                    'choice_translation_domain' => false,
                ]
            )
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
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
            ->add('id', null, ['label' => 'proof_request.show.id'])
            ->add('createdAt', null, ['label' => 'proof_request.show.createdAt'])
            ->add('playerRequesting', null, ['label' => 'proof_request.show.playerRequesting'])
            ->add('playerResponding', null, ['label' => 'proof_request.show.playerResponding'])
            ->add('playerChart', null, ['label' => 'proof_request.show.playerChart'])
            ->add('message', null, ['label' => 'proof_request.show.message'])
            ->add('status', null, ['label' => 'proof_request.show.status']);
    }

    /**
     * @param $object
     */
    public function preValidate($object): void
    {
        $player = $this->getPlayer();

        if ($player) {
            if (
                ($object->getPlayerRequesting()->getId() === $player->getId())
                || ($object->getPlayerChart()->getPlayer()->getId() === $player->getId())
            ) {
                $this->requestStack->getSession()->getFlashBag()->add(
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
        if (in_array($originalObject['status'], array(ProofRequestStatus::ACCEPTED, ProofRequestStatus::REFUSED), true)) {
            $object->setStatus($originalObject['status']);
        }
    }

    /**
     * @return Player
     */
    private function getPlayer(): Player
    {
        /** @var EntityManager $em */
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $user = $this->security->getUser();
        return $em->getRepository('VideoGamesRecords\CoreBundle\Entity\Player')->getPlayerFromUser($user);
    }
}
