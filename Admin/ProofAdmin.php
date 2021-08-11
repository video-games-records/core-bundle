<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use Doctrine\ORM\EntityManager;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use ProjetNormandie\MessageBundle\Service\Messager;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus;
use VideoGamesRecords\CoreBundle\Entity\Proof;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\DoctrineORMAdminBundle\Filter\ModelAutocompleteFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ChoiceFilter;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\ORMException;
use DateTime;

class ProofAdmin extends AbstractAdmin
{
    //protected $baseRouteName = 'vgrcorebundle_admin_proof';

    /** @var Messager */
    private $messager;

    public function setMessager(Messager $messager)
    {
        $this->messager = $messager;
    }

    /**
     * @return string
     */
    private function getLibGame(): string
    {
        return 'libGameEn';
        //return ($this->getRequest()->getLocale() == 'fr') ? 'libGameFr' : 'libGameEn';
    }

    /**
     * @return string
     */
    private function getLibGroup(): string
    {
        return 'libGroupEn';
        //return ($this->getRequest()->getLocale() == 'fr') ? 'libGroupFr' : 'libGroupEn';
    }

    /**
     * @return string
     */
    private function getLibChart(): string
    {
        return 'libChartEn';
        //return ($this->getRequest()->getLocale() == 'fr') ? 'libChartFr' : 'libChartEn';
    }

    /**
     * @param ProxyQueryInterface $query
     * @return ProxyQueryInterface
     */
    protected function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $query = parent::configureQuery($query);
        $query
            ->innerJoin($query->getRootAliases()[0]  . '.chart', 'chr')
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
                    'label' => 'playerResponding',
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
                    'label' => 'PlayerChart',
                ]
            )
            ->add(
                'status',
                ChoiceType::class,
                [
                    'label' => 'Status',
                    'choices' => Proof::getStatusChoices(),
                ]
            )
            ->add('response', CKEditorType::class, [
                'label' => 'Response',
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
            ->add('id')
            ->add('player', ModelAutocompleteFilter::class, ['label' => 'Player'], null, [
                'property' => 'pseudo',
            ])
            ->add('chart.group.game', ModelAutocompleteFilter::class, ['label' => 'Game'], null, [
                'property' => $this->getLibGame(),
            ])
            ->add('status', ChoiceFilter::class, [], ChoiceType::class, [
                'choices' => Proof::getStatusChoices(),
                'multiple' => false,
            ])
            ->add('playerResponding', ModelAutocompleteFilter::class, [], null, [
                'property' => 'pseudo',
            ]);
    }


    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id')
            ->add('player', null, [
                'associated_property' => 'pseudo',
                'label' => 'Player',
            ])
            ->add('chart.group.game', null, [
                'associated_property' =>  $this->getLibGame(),
                'label' => 'Game',
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
                'label' => 'Group',
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
                'label' => 'Chart',
            ])
            ->addIdentifier('type')
            ->add('playerResponding', null, [
                'associated_property' => 'pseudo',
                'label' => 'PlayerResponding',
            ])
            ->add(
                'status',
                'choice',
                [
                    'label' => 'Status',
                    'editable' => true,
                    'choices' => Proof::getStatusChoices(),
                ]
            )
            ->add('created_at', 'datetime', ['label' => 'Created At'])
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
            ->add('Player', null, ['label' => 'Player'])
            ->add('chart.group.game', null, array(
                'associated_property' => $this->getLibGame(),
                'label' => 'Game',
            ))
            ->add('chart.group', null, array(
                'associated_property' => $this->getLibGroup(),
                'label' => 'Group',
            ))
            ->add('chart', null, array(
                'associated_property' => $this->getLibChart(),
                'label' => 'Chart',
            ))
            ->add('created_at', 'datetime', ['label' => 'Created At'])
            ->add('updated_at', 'datetime', ['label' => 'Updated At'])
            ->add('checkedAt', 'datetime', ['label' => 'Checked At'])
            ->add('playerChart')
            ->add('picture')
            ->add('video')
            ->add('payerResponding')
            ->add('status');
    }

    /**
     * @param $object
     */
    public function preValidate($object): void
    {
        $player = $this->getPlayer();

        if ($object->getPlayerChart() != null) {
            if ($object->getPlayerChart()->getPlayer()->getId() === $player->getId()) {
                $this->getConfigurationPool()->getContainer()->get('session')->getFlashBag()->add(
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
     * @return bool|void
     * @throws ORMException
     */
    public function preUpdate($object): void
    {
        /** @var EntityManager $em */
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $originalObject = $em->getUnitOfWork()->getOriginalEntityData($object);
        $player = $this->getPlayer();

        // Cant change status final
        if (in_array($originalObject['status'], array(Proof::STATUS_ACCEPTED, Proof::STATUS_REFUSED), true)) {
            $object->setStatus($originalObject['status']);
        }

        $setPlayerResponding = false;
        if ($object->getPlayerChart() == null) {
            $setPlayerResponding = true;
            $object->setStatus(Proof::STATUS_CLOSED);
        }

        // ACCEPTED
        if ($originalObject['status'] === Proof::STATUS_IN_PROGRESS && $object->getStatus() === Proof::STATUS_ACCEPTED) {
            /** @var PlayerChart $playerChart */
            $object->getPlayerChart()->setStatus($em->getReference(PlayerChartStatus::class, PlayerChartStatus::ID_STATUS_PROOVED));
            $setPlayerResponding = true;
            // Send MP (1)
            $recipient = $object->getPlayerChart()->getPlayer()->getUser();
            $url = '/' . $recipient->getLocale() . '/' . $object->getPlayerChart()->getUrl();
            $this->messager->send(
                $this->trans('proof.proof.accept.object', array(), null, $recipient->getLocale()),
                sprintf(
                    $this->trans('proof.proof.accept.message', array(), null, $recipient->getLocale()),
                    $recipient->getUsername(),
                    $url,
                    $object->getPlayerChart()->getChart()->getCompleteName($recipient->getLocale()),
                    $object->getResponse()
                ),
                $em->getReference('VideoGamesRecords\CoreBundle\Entity\User\UserInterface', 0),
                $recipient,
                'VGR_PROOF'
            );
        }

        // REFUSED
        if ($originalObject['status'] === Proof::STATUS_IN_PROGRESS && $object->getStatus() === Proof::STATUS_REFUSED) {
            /** @var PlayerChart $playerChart */
            $idStatus = ($object->getPlayerChart()->getStatus()->getId() === PlayerChartStatus::ID_STATUS_NORMAL_SEND_PROOF)
                ? PlayerChartStatus::ID_STATUS_NORMAL : PlayerChartStatus::ID_STATUS_INVESTIGATION;
            $object->getPlayerChart()->setStatus($em->getReference(PlayerChartStatus::class, $idStatus));
            $setPlayerResponding = true;
            // Send MP (1)
            $recipient = $object->getPlayerChart()->getPlayer()->getUser();
            $url = '/' . $recipient->getLocale() . '/' . $object->getPlayerChart()->getUrl();
            $this->messager->send(
                $this->trans('proof.proof.refuse.object', array(), null, $recipient->getLocale()),
                sprintf(
                    $this->trans('proof.proof.refuse.message', array(), null, $recipient->getLocale()),
                    $recipient->getUsername(),
                    $url,
                    $object->getPlayerChart()->getChart()->getCompleteName($recipient->getLocale()),
                    $object->getResponse()
                ),
                $em->getReference('VideoGamesRecords\CoreBundle\Entity\User\UserInterface', 0),
                $recipient,
                'VGR_PROOF'
            );
        }

        // Player Responding
        if ($setPlayerResponding) {
            $object->setPlayerResponding($player);
            $object->setCheckedAt(new DateTime());
        }
    }

    /**
     * @return mixed
     */
    private function getPlayer()
    {
        /** @var EntityManager $em */
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        return $em->getRepository('VideoGamesRecordsCoreBundle:Player')->getPlayerFromUser($user);
    }
}
