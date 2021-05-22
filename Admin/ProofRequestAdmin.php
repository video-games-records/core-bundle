<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use ProjetNormandie\MessageBundle\Service\Messager;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus;
use VideoGamesRecords\CoreBundle\Entity\ProofRequest;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\DoctrineORMAdminBundle\Filter\ModelAutocompleteFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ChoiceFilter;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ProofRequestAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_proofrequest';

    /** @var Messager */
    private $messager;

    public function setMessager(Messager $messager): void
    {
        $this->messager = $messager;
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
            ->add(
                'status',
                ChoiceType::class,
                [
                    'label' => 'Status',
                    'choices' => ProofRequest::getStatusChoices(),
                ]
            )
            ->add('message', CKEditorType::class, [
                'label' => 'Message',
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
            ->add('status', ChoiceFilter::class, [], ChoiceType::class, [
                'choices' => ProofRequest::getStatusChoices(),
                'multiple' => false,
            ])
            ->add('playerRequesting', ModelAutocompleteFilter::class, [], null, [
                'property' => 'pseudo',
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
            ->add('createdAt')
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
     * @param ShowMapper $show
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('createdAt')
            ->add('playerRequesting')
            ->add('playerResponding')
            ->add('playerChart')
            ->add('message')
            ->add('status');
    }

    /**
     * @param $object
     */
    public function preValidate($object): void
    {
        $player =  $this->getPlayer();

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
     * @param $object
     * @throws ORMException
     */
    public function preUpdate($object): void
    {
        /** @var EntityManager $em */
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $originalObject = $em->getUnitOfWork()->getOriginalEntityData($object);
        $player = $this->getPlayer();

        $setPlayerResponding = false;

        // Cant change status final
        if (in_array($originalObject['status'], array(ProofRequest::STATUS_ACCEPTED, ProofRequest::STATUS_REFUSED), true)) {
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
            $url = $recipient->getLocale() . '/' . $object->getPlayerChart()->getUrl();
            $this->messager->send(
                $this->trans('proof.request.confirm.object', array(), null, $recipient->getLocale()),
                sprintf(
                    $this->trans('proof.request.confirm.message', array(), null, $recipient->getLocale()),
                    $recipient->getUsername(),
                    $url,
                    $object->getPlayerChart()->getChart()->getCompleteName($recipient->getLocale())
                ),
                $em->getReference('VideoGamesRecords\CoreBundle\Entity\User\UserInterface', 0),
                $recipient,
                'VGR_PROOF_REQUEST'
            );
            // Send MP (2)
            $recipient = $object->getPlayerRequesting()->getUser();
            $this->messager->send(
                $this->trans('proof.request.accept.object', array(), null, $recipient->getLocale()),
                sprintf(
                    $this->trans('proof.request.accept.message', array(), null, $recipient->getLocale()),
                    $recipient->getUsername(),
                    $url,
                    $object->getPlayerChart()->getChart()->getCompleteName($recipient->getLocale()),
                    $object->getPlayerChart()->getPlayer()->getPseudo(),
                    $object->getResponse()
                ),
                $em->getReference('VideoGamesRecords\CoreBundle\Entity\User\UserInterface', 0),
                $recipient,
                'VGR_PROOF_REQUEST'
            );
        }

        // REFUSED
        if ($originalObject['status'] === ProofRequest::STATUS_IN_PROGRESS && $object->getStatus() === ProofRequest::STATUS_REFUSED) {
            $object->getPlayerChart()->setStatus(
                $em->getReference(PlayerChartStatus::class, PlayerChartStatus::ID_STATUS_NORMAL)
            );
            $setPlayerResponding = true;
            $recipient = $object->getPlayerRequesting()->getUser();
            $url = $recipient->getLocale() . '/' . $object->getPlayerChart()->getUrl();
            $this->messager->send(
                $this->trans('proof.request.refuse.object', array(), null, $recipient->getLocale()),
                sprintf(
                    $this->trans('proof.request.refuse.message', array(), null, $recipient->getLocale()),
                    $recipient->getUsername(),
                    $url,
                    $object->getPlayerChart()->getChart()->getCompleteName($recipient->getLocale()),
                    $object->getPlayerChart()->getPlayer()->getPseudo(),
                    $object->getResponse()
                ),
                $em->getReference('VideoGamesRecords\CoreBundle\Entity\User\UserInterface', 0),
                $recipient,
                'VGR_PROOF_REQUEST'
            );
        }

        if ($setPlayerResponding) {
            $object->setPlayerResponding($player);
            $object->setDateAcceptance(new DateTime());
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
