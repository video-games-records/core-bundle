<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use VideoGamesRecords\CoreBundle\ValueObject\ProofStatus;

class ProofAdminController extends CRUDController
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * Action d'édition personnalisée pour gérer la validation des preuves
     */
    public function editAction(Request $request): Response
    {
        $object = $this->assertObjectExists($request, true);
        $this->checkParentChildAssociation($request, $object);

        $this->admin->checkAccess('edit', $object);

        $preResponse = $this->preEdit($request, $object);
        if (null !== $preResponse) {
            return $preResponse;
        }

        $this->admin->setSubject($object);
        $form = $this->admin->getForm();
        $form->setData($object);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $isFormValid = $form->isValid();

            // Gestion spéciale pour la validation des preuves
            if ($isFormValid && $this->isProofValidationSubmission($request, $object)) {
                $this->handleProofValidation($object, $request);
            }

            // Validation et redirection si tout est OK
            if ($isFormValid) {
                $submittedObject = $form->getData();
                $this->admin->setSubject($submittedObject);

                try {
                    $submittedObject = $this->admin->update($submittedObject);

                    if ($this->isXmlHttpRequest($request)) {
                        return $this->handleXmlHttpRequestSuccessResponse($request, $submittedObject);
                    }

                    $this->addFlash(
                        'sonata_flash_success',
                        $this->getProofValidationMessage($submittedObject)
                    );

                    // Redirection après succès vers la prochaine preuve
                    $nextProofRedirect = $this->getNextProofRedirect($submittedObject);
                    if ($nextProofRedirect) {
                        return $nextProofRedirect;
                    }

                    if (null !== ($response = $this->redirectTo($request, $submittedObject))) {
                        return $response;
                    }
                } catch (\Throwable $e) {
                    $this->handleModelManagerThrowable($e);

                    $isFormValid = false;
                }
            }

            // Message d'erreur si la validation a échoué
            if (!$isFormValid) {
                $this->addFlash(
                    'sonata_flash_error',
                    $this->trans(
                        'flash_edit_error',
                        ['%name%' => $this->escapeHtml($this->admin->toString($object))],
                        'SonataAdminBundle'
                    )
                );
            }
        }

        $formView = $form->createView();
        $this->setFormTheme($formView, $this->admin->getFormTheme());

        return $this->render(
            $this->admin->getTemplateRegistry()->getTemplate('edit'),
            [
                'action' => 'edit',
                'form' => $formView,
                'object' => $object,
                'objectId' => $this->admin->getNormalizedIdentifier($object),
            ]
        );
    }

    /**
     * Vérifie si la soumission concerne une validation de preuve
     */
    private function isProofValidationSubmission(Request $request, $proof): bool
    {
        $formData = $request->request->all();

        // Cherche le champ status dans les données du formulaire
        foreach ($formData as $key => $data) {
            if (is_array($data) && isset($data['status'])) {
                $newStatus = $data['status'];
                $currentStatus = $proof->getStatus()->getValue();

                // Vérifie si c'est un changement de IN_PROGRESS vers ACCEPTED ou REFUSED
                return $currentStatus === ProofStatus::IN_PROGRESS &&
                    in_array($newStatus, [ProofStatus::ACCEPTED, ProofStatus::REFUSED]);
            }
        }

        return false;
    }

    /**
     * Gère la logique de validation de preuve
     */
    private function handleProofValidation($proof, Request $request): void
    {
        // Récupère le nouveau statut depuis la requête
        $formData = $request->request->all();
        $newStatus = null;

        foreach ($formData as $key => $data) {
            if (is_array($data) && isset($data['status'])) {
                $newStatus = $data['status'];
                break;
            }
        }

        if (!$newStatus) {
            return;
        }

        try {
            // Logique spécifique selon le statut
            if ($newStatus === ProofStatus::ACCEPTED) {
                $this->handleProofAccepted($proof);
            } elseif ($newStatus === ProofStatus::REFUSED) {
                $this->handleProofRefused($proof);
            }
        } catch (\Exception $e) {
            $this->addFlash(
                'sonata_flash_error',
                'Erreur lors de la validation de la preuve : ' . $e->getMessage()
            );
        }
    }

    /**
     * Logique à exécuter quand une preuve est acceptée
     */
    private function handleProofAccepted($proof): void
    {
        // La logique est déjà gérée dans ProofListener::postUpdate()
        // Mais vous pouvez ajouter ici des traitements supplémentaires comme :
        // - Logging spécifique
        // - Notifications custom
        // - Mise à jour de statistiques

        $playerChart = $proof->getPlayerChart();
        if ($playerChart) {
            // Le statut du PlayerChart sera automatiquement mis à jour par le listener
            // à PlayerChartStatus::ID_STATUS_PROOVED
        }
    }

    /**
     * Logique à exécuter quand une preuve est refusée
     */
    private function handleProofRefused($proof): void
    {
        // La logique est déjà gérée dans ProofListener::postUpdate()
        // Mais vous pouvez ajouter ici des traitements supplémentaires

        $playerChart = $proof->getPlayerChart();
        if ($playerChart) {
            // Le statut du PlayerChart sera automatiquement ajusté par le listener
        }
    }

    /**
     * Génère le message de confirmation selon le statut
     */
    private function getProofValidationMessage($proof): string
    {
        $status = $proof->getStatus()->getValue();
        $playerName = $proof->getPlayerChart() ?
            $proof->getPlayerChart()->getPlayer()->getPseudo() :
            $this->trans('proof.unknown.player');

        switch ($status) {
            case ProofStatus::ACCEPTED:
                return $this->trans(
                    'proof.success.accepted',
                    ['%player%' => $playerName]
                );
            case ProofStatus::REFUSED:
                return $this->trans(
                    'proof.success.refused',
                    ['%player%' => $playerName]
                );
            default:
                return $this->trans(
                    'flash_edit_success',
                    ['%name%' => $this->escapeHtml($this->admin->toString($proof))],
                    'SonataAdminBundle'
                );
        }
    }

    /**
     * Trouve la prochaine preuve à valider dans le même jeu
     */
    private function getNextProofRedirect($currentProof): ?RedirectResponse
    {
        // Récupère le jeu de la preuve actuelle
        $currentGame = $currentProof->getChart()->getGroup()->getGame();

        $proofRepository = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Proof');

        $nextProof = $proofRepository->findNextInProgressByGame(
            $currentGame,
            $currentProof->getId()
        );

        if ($nextProof) {
            // Message informatif sur le nombre de preuves restantes
            $remainingCount = $proofRepository->countInProgressByGame($currentGame);
            $this->addFlash(
                'sonata_flash_info',
                $this->trans(
                    'proof.next.redirect.info',
                    [
                        '%game%' => $currentGame->getName(),
                        '%count%' => $remainingCount - 1, // -1 car on vient de traiter une preuve
                        '%next_id%' => $nextProof->getId()
                    ]
                )
            );

            // Redirection vers la prochaine preuve
            return new RedirectResponse(
                $this->admin->generateUrl('edit', ['id' => $nextProof->getId()])
            );
        }

        // Aucune autre preuve dans le même jeu, redirection vers la liste
        return new RedirectResponse(
            $this->admin->generateUrl('list')
        );
    }
}
