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
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\HttpFoundation\Response;
use Sonata\DoctrineORMAdminBundle\Filter\ModelAutocompleteFilter;
use Sonata\AdminBundle\Form\Type\ModelListType;

class ProofAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_proof';

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
                    'value' => Proof::STATUS_IN_PROGRESS,
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
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('status')
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
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('picture')
            ->add('video')
            ->add('payerResponding')
            ->add('status')
            ->add('player')
            ->add('Chart')
            ->add('playerChart');
    }

    /**
     * @param $object
     * @return Response
     */
    public function preValidate($object)
    {
        /** @var \App\Entity\User */
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        $player = $user->getRelation();

        if ($object->getPlayerChart() != null) {
            if ($object->getPlayerChart()->getPlayer()->getId() === $player->getId()) {
                $this->getConfigurationPool()->getContainer()->get('session')->getFlashBag()->add(
                    'error',
                    "You can't update this proof"
                );
                return new Response();
            }
        }
    }

    /**
     * @param object $object
     * @throws \Doctrine\ORM\ORMException
     */
    public function preUpdate($object)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $originalObject = $em->getUnitOfWork()->getOriginalEntityData($object);
        $admin = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();

        // Cant change status final
        if (\in_array($originalObject['status'], array(Proof::STATUS_ACCEPTED, Proof::STATUS_REFUSED), true)) {
            $object->setStatus($originalObject['status']);
        }

        $setPlayerResponding = false;
        if ($object->getPlayerChart() == null) {
            $setPlayerResponding = true;
            $object->setStatus(Proof::STATUS_CLOSED);
        }

        // ACCEPTED
        if ($originalObject['status'] === Proof::STATUS_IN_PROGRESS && $object->getStatus() === Proof::STATUS_ACCEPTED) {
            /** @var \VideoGamesRecords\CoreBundle\Entity\PlayerChart $playerChart */
            $object->getPlayerChart()->setStatus($em->getReference(PlayerChartStatus::class, PlayerChartStatus::ID_STATUS_PROOVED));
            $setPlayerResponding = true;
            // Send MP (1)
            $recipient = $object->getPlayerChart()->getPlayer()->getUser();
            $em->getRepository('VideoGamesRecordsCoreBundle:MessageInterface')->create(
                array(
                    'type' => 'VGR_PROOF',
                    'object' => $this->trans('proof.proof.accept.object', array(), null, $recipient->getLocale()),
                    'message' => sprintf(
                        $this->trans('proof.proof.accept.message', array(), null, $recipient->getLocale()),
                        $recipient->getUsername(),
                        $object->getPlayerChart()->getChart()->getCompleteName($recipient->getLocale())
                    ),
                    'sender' => $em->getReference('VideoGamesRecords\CoreBundle\Entity\User\UserInterface', 0),
                    'recipient' => $recipient,
                )
            );
        }

        // REFUSED
        if ($originalObject['status'] === Proof::STATUS_IN_PROGRESS && $object->getStatus() === Proof::STATUS_REFUSED) {
            /** @var \VideoGamesRecords\CoreBundle\Entity\PlayerChart $playerChart */
            $idStatus = ($object->getPlayerChart()->getStatus()->getId() === PlayerChartStatus::ID_STATUS_NORMAL_SEND_PROOF) ? PlayerChartStatus::ID_STATUS_NORMAL : PlayerChartStatus::ID_STATUS_INVESTIGATION;
            $object->getPlayerChart()->setStatus($em->getReference(PlayerChartStatus::class, $idStatus));
            $setPlayerResponding = true;
            // Send MP (1)
            $recipient = $object->getPlayerChart()->getPlayer()->getUser();
            $em->getRepository('VideoGamesRecordsCoreBundle:MessageInterface')->create(
                array(
                    'type' => 'VGR_PROOF',
                    'object' => $this->trans('proof.proof.refuse.object', array(), null, $recipient->getLocale()),
                    'message' => sprintf(
                        $this->trans('proof.proof.refuse.message', array(), null, $recipient->getLocale()),
                        $recipient->getUsername(),
                        $object->getPlayerChart()->getChart()->getCompleteName($recipient->getLocale())
                    ),
                    'sender' => $em->getReference('VideoGamesRecords\CoreBundle\Entity\User\UserInterface', 0),
                    'recipient' => $recipient,
                )
            );
        }

        // Player Responding
        if ($setPlayerResponding) {
            $player = $admin->getRelation();
            $object->setPlayerResponding($player);
        }
    }
}
