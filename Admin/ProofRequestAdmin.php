<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus;
use VideoGamesRecords\CoreBundle\Entity\ProofRequest;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\HttpFoundation\Response;

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
     * @inheritdoc
     */
    public function getFilterParameters()
    {
        $this->datagridValues = array_merge(
            array(
                'status' => array(
                    'value' => ProofRequest::STATUS_IN_PROGRESS,
                )
            ),
            $this->datagridValues
        );
        return parent::getFilterParameters();
    }

    /**
     * @inheritdoc
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add(
                'playerRequesting',
                'sonata_type_model_list',
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
                'sonata_type_model_list',
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
            ->add('message', 'textarea', [
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
            ->add('playerRequesting', 'doctrine_orm_model_autocomplete', [], null, [
                'property' => 'pseudo',
            ])
            ->add('playerResponding', 'doctrine_orm_model_autocomplete', [], null, [
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
            ->add('playerRequesting')
            ->add('playerResponding')
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
        $player =  $em->getRepository('VideoGamesRecordsCoreBundle:Player')
            ->getPlayerFromUser($user);

        if ($player) {
            if (($object->getPlayerRequesting()->getId() === $player->getId())
            || ($object->getPlayerChart()->getPlayer()->getId() === $player->getId())) {
                $this->getConfigurationPool()->getContainer()->get('session')->getFlashBag()->add(
                    'error',
                    "You can't update this request"
                );
                return new Response();
            }
        }
    }


    /**
     * @param $object
     * @throws \Exception
     */
    public function preUpdate($object)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $originalObject = $em->getUnitOfWork()->getOriginalEntityData($object);

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
        }

        // REFUSED
        if ($originalObject['status'] === ProofRequest::STATUS_IN_PROGRESS && $object->getStatus() === ProofRequest::STATUS_REFUSED) {
            $object->getPlayerChart()->setStatus(
                $em->getReference(PlayerChartStatus::class, PlayerChartStatus::ID_STATUS_NORMAL)
            );
            $setPlayerResponding = true;
        }

        if ($setPlayerResponding) {
            $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
            $player =  $em->getRepository('VideoGamesRecordsCoreBundle:Player')
                ->getPlayerFromUser($user);
            $object->setPlayerResponding($player);
            $object->setDateAcceptance(new \DateTime());
        }
    }
}
