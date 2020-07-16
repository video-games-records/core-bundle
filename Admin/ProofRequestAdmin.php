<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus;
use VideoGamesRecords\CoreBundle\Entity\Proof;
use VideoGamesRecords\CoreBundle\Entity\ProofRequest;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\HttpFoundation\Response;
use Sonata\DoctrineORMAdminBundle\Filter\ModelAutocompleteFilter;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use VideoGamesRecords\CoreBundle\Entity\Message\Message;
use Sonata\AdminBundle\Form\Type\Operator\EqualOperatorType;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ProofRequestAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_proofrequest';


    /**
     * @inheritdoc
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
            'value' => ProofRequest::STATUS_IN_PROGRESS,
        ];
    }

    /**
     * @inheritdoc
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
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
                    'label' => 'PlayerRequesting',
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
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                'required' => true,
                'attr' => array(
                    'readonly' => true,
                )
            ])
            ->add(
                'status',
                ChoiceType::class,
                [
                    'label' => 'Status',
                    'choices' => ProofRequest::getStatusChoices(),
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
            ->add('playerRequesting', ModelAutocompleteFilter::class, [], null, [
                'property' => 'pseudo',
            ])
            ->add('playerResponding', ModelAutocompleteFilter::class, [], null, [
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
            ->add('playerRequesting', null, [
                'associated_property' => 'pseudo',
                'label' => 'PlayerRequesting',
            ])
            ->add('playerResponding', null, [
                'associated_property' => 'pseudo',
                'label' => 'PlayerResponding',
            ])
            ->add('playerChart.player', null, [
                'label' => 'Player'
            ])
            ->add('playerChart.chart', null, [
                'label' => 'Chart'
            ])
            ->add('message', 'text', [
                'header_style' => 'width: 30%'
            ])
            ->add(
                'status',
                'choice',
                [
                    'label' => 'Status',
                    'editable' => true,
                    'choices' => ProofRequest::getStatusChoices(),
                ]
            )
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'view_chart' => [
                        'template' => 'VideoGamesRecordsCoreBundle:Admin:view_chart_link.html.twig'
                    ],
                ],
                'header_style' => 'width: 220px'
            ]);
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('playerRequesting')
            ->add('playerResponding')
            ->add('playerChart')
            ->add('message')
            ->add('status');
    }

    /**
     * @param $object
     * @return Response
     */
    public function preValidate($object)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getModelManager()->getEntityManager($this->getClass());

        /** @var \App\Entity\User */
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        $player =  $user->getRelation();

        if ($player) {
            if (($object->getPlayerRequesting()->getId() === $player->getId())
            || ($object->getPlayerChart()->getPlayer()->getId() === $player->getId())) {
                $this->getConfigurationPool()->getContainer()->get('session')->getFlashBag()->add(
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
     * @param object $object
     * @throws \Doctrine\ORM\ORMException
     */
    public function preUpdate($object)
    {
        return;
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $originalObject = $em->getUnitOfWork()->getOriginalEntityData($object);
        $admin = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();

        $setPlayerResponding = false;

        // Cant change status final
        if (\in_array($originalObject['status'], array(ProofRequest::STATUS_ACCEPTED, ProofRequest::STATUS_REFUSED), true)) {
            $object->setStatus($originalObject['status']);
        }

        // ACCEPTED
        if ($originalObject['status'] === ProofRequest::STATUS_IN_PROGRESS && $object->getStatus() === ProofRequest::STATUS_ACCEPTED) {
            $object->getPlayerChart()->setStatus(
                $em->getReference(PlayerChartStatus::class, PlayerChartStatus::ID_STATUS_INVESTIGATION)
            );
            $setPlayerResponding = true;
            // Send MP (1)
            $recipient = $object->getPlayerChart()->getPlayer()->getUser();
            $em->getRepository('VideoGamesRecordsCoreBundle:MessageInterface')->create(
                array(
                    'type' => 'VGR_PROOF_REQUEST',
                    'object' => $this->trans('proof.request.confirm.object', array(), null, $recipient->getLocale()),
                    'message' => sprintf(
                        $this->trans('proof.request.confirm.message', array(), null, $recipient->getLocale()),
                        $recipient->getUsername(),
                        $object->getPlayerChart()->getChart()->getCompleteName($recipient->getLocale())
                    ),
                    'sender' => $em->getReference('VideoGamesRecords\CoreBundle\Entity\User\UserInterface', 0),
                    'recipient' => $recipient,
                )
            );
            // Send MP (2)
            $recipient = $object->getPlayerRequesting()->getUser();
            $em->getRepository('VideoGamesRecordsCoreBundle:MessageInterface')->create(
                array(
                    'type' => 'VGR_PROOF_REQUEST',
                    'object' => $this->trans('proof.request.accept.object', array(), null, $recipient->getLocale()),
                    'message' => sprintf(
                        $this->trans('proof.request.accept.message', array(), null, $recipient->getLocale()),
                        $recipient->getUsername(),
                        $object->getPlayerChart()->getChart()->getCompleteName($recipient->getLocale()),
                        $object->getPlayerChart()->getPlayer()->getPseudo()
                    ),
                    'sender' => $em->getReference('VideoGamesRecords\CoreBundle\Entity\User\UserInterface', 0),
                    'recipient' => $recipient,
                )
            );
        }

        // REFUSED
        if ($originalObject['status'] === ProofRequest::STATUS_IN_PROGRESS && $object->getStatus() === ProofRequest::STATUS_REFUSED) {
            $object->getPlayerChart()->setStatus(
                $em->getReference(PlayerChartStatus::class, PlayerChartStatus::ID_STATUS_NORMAL)
            );
            $setPlayerResponding = true;
            $recipient = $object->getPlayerRequesting()->getUser();
            $em->getRepository('VideoGamesRecordsCoreBundle:MessageInterface')->create(
                array(
                    'type' => 'VGR_PROOF_REQUEST',
                    'object' => $this->trans('proof.request.refuse.object', array(), null, $recipient->getLocale()),
                    'message' => sprintf(
                        $this->trans('proof.request.refuse.message', array(), null, $recipient->getLocale()),
                        $recipient->getUsername(),
                        $object->getPlayerChart()->getChart()->getCompleteName($recipient->getLocale()),
                        $object->getPlayerChart()->getPlayer()->getPseudo()
                    ),
                    'sender' => $em->getReference('VideoGamesRecords\CoreBundle\Entity\User\UserInterface', 0),
                    'recipient' => $recipient,
                )
            );
        }

        if ($setPlayerResponding) {
            $object->setPlayerResponding($admin->getRelation());
            $object->setDateAcceptance(new \DateTime());
        }
    }
}
