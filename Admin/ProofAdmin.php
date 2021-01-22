<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use Doctrine\ORM\EntityManager;
use ProjetNormandie\MessageBundle\Service\Messager;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus;
use VideoGamesRecords\CoreBundle\Entity\Proof;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\HttpFoundation\Response;
use Sonata\DoctrineORMAdminBundle\Filter\ModelAutocompleteFilter;
//use Sonata\DoctrineORMAdminBundle\Filter\StringListFilter;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Form\Type\Operator\EqualOperatorType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\ORMException;

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
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('create')
            ->remove('delete')
            ->remove('export');
    }

    /**
     * @param array $filterValues
     */
    protected function configureDefaultFilterValues(array &$filterValues)
    {
        $filterValues['status'] = [
             'type'  => EqualOperatorType::TYPE_EQUAL,
             'value' => Proof::STATUS_IN_PROGRESS,
        ];
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add(
                'picture',
                ModelListType::class,
                [
                    'data_class' => null,
                    'btn_add' => false,
                    'btn_list' => false,
                    'btn_edit' => false,
                    'btn_delete' => false,
                    'btn_catalogue' => false,
                    'label' => 'picture',
                ]
            )
            ->add(
                'video',
                ModelListType::class,
                [
                    'data_class' => null,
                    'btn_add' => false,
                    'btn_list' => false,
                    'btn_edit' => false,
                    'btn_delete' => false,
                    'btn_catalogue' => false,
                    'label' => 'video',
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
                    'label' => 'playerResponding',
                ]
            )
            ->add(
                'status',
                ChoiceType::class,
                [
                    'label' => 'Status',
                    'choices' => Proof::getStatusChoices(),
                ]
            );
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('status')
            ->add('playerChart.player', ModelAutocompleteFilter::class, ['label' => 'Player'], null, [
                'property' => 'pseudo',
            ])
            /*->add('status', StringListFilter::class, [], ChoiceType::class, [
                'choices' => Proof::getStatusChoices(),
                'multiple' => false,
            ])*/
            ->add('playerResponding', ModelAutocompleteFilter::class, [], null, [
                'property' => 'pseudo',
            ]);
    }


    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('playerChart.player', null, [
                'associated_property' => 'pseudo',
                'label' => 'Player',
            ])
            ->add('picture', null, [
                'associated_property' => 'path',
                'label' => 'Picture',
            ])
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
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('picture')
            ->add('video')
            ->add('payerResponding')
            ->add('status')
            ->add('playerChart.player', null, ['label' => 'Player'])
            ->add('Chart')
            ->add('playerChart');
    }

    /**
     * @param $object
     */
    public function preValidate($object)
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
    public function preUpdate($object)
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
            $url = '#/' . $recipient->getLocale() . '/' . $object->getPlayerChart()->getUrl();
            $this->messager->send(
                $this->trans('proof.proof.accept.object', array(), null, $recipient->getLocale()),
                sprintf(
                    $this->trans('proof.proof.accept.message', array(), null, $recipient->getLocale()),
                    $recipient->getUsername(),
                    $url,
                    $object->getPlayerChart()->getChart()->getCompleteName($recipient->getLocale())
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
            $url = '#/' . $recipient->getLocale() . '/' . $object->getPlayerChart()->getUrl();
            $this->messager->send(
                $this->trans('proof.proof.refuse.object', array(), null, $recipient->getLocale()),
                sprintf(
                    $this->trans('proof.proof.refuse.message', array(), null, $recipient->getLocale()),
                    $recipient->getUsername(),
                    $url,
                    $object->getPlayerChart()->getChart()->getCompleteName($recipient->getLocale())
                ),
                $em->getReference('VideoGamesRecords\CoreBundle\Entity\User\UserInterface', 0),
                $recipient,
                'VGR_PROOF'
            );
        }

        // Player Responding
        if ($setPlayerResponding) {
            $object->setPlayerResponding($player);
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
