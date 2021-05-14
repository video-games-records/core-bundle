<?php
namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PlayerChartLibAdmin extends AbstractAdmin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $form): void
    {
        $playerChart = $this->getRequest()->getSession()->get('vgrcorebundle_admin_playerChart.subject');

        /*if (($this->hasRequest()) && ($this->isCurrentRoute('create'))) {
            $idPlayerChart = $this->getRequest()->get('idGame', null);
            if ($idGame !== null) {
                $this->getRequest()->getSession()->set('vgrcorebundle_admin_group.idGame', $idGame);
            }

            if ($this->getRequest()->getSession()->has('vgrcorebundle_admin_group.idGame')) {
                $idGame= $this->getRequest()->getSession()->get('vgrcorebundle_admin_group.idGame');
                $entityManager = $this->getModelManager()
                    ->getEntityManager('VideoGamesRecords\CoreBundle\Entity\Game');
                $game = $entityManager->getReference('VideoGamesRecords\CoreBundle\Entity\Game', $idGame);
            }
        }*/

        $form
            ->add('id', TextType::class, [
                'label' => 'id',
                'attr' => [
                    'readonly' => true,
                ]
            ])
            /*->add('libChart', ModelListType::class, [
                'data_class' => null,
                'btn_add' => false,
                'btn_list' => true,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => true,
                'label' => 'Lib',
            ])*/
            ->add(
                'libChart',
                null,
                [
                    'required' => true,
                    'query_builder' =>
                        function($er) use ($playerChart) {
                            $qb = $er->createQueryBuilder('l');
                            $qb->where('l.chart = :chart');
                            $qb->setParameter('chart', $playerChart->getChart());
                            return $qb;
                        }
                ]
            )
            ->add('value', TextType::class, [
                'label' => 'Value',
                'required' => true,
            ]);
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id')
            ->add('value');
    }
}
