<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use VideoGamesRecords\CoreBundle\Entity\Platform;
use VideoGamesRecords\CoreBundle\Entity\Game;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\DBAL\DBALException;
use FOS\UserBundle\Model\UserManagerInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class PlayerChartController
 */
class PlayerChartController extends Controller
{

    private $em;
    private $userManager;

    public function __construct(UserManagerInterface $userManager, EntityManagerInterface $em)
    {
        $this->userManager = $userManager;
        $this->em = $em;
    }

    /**
     *
     */
    public function getPlayer()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')
            ->getPlayerFromUser($this->getUser());
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function majPlatform(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $idGame = $data['idGame'];
        $idPlatform = $data['idPlatform'];

        $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerChart')->majPlatform(
            $this->getPlayer(),
            $this->em->getReference(Game::class, $idGame),
            $this->em->getReference(Platform::class, $idPlatform)
        );
        return new JsonResponse(['data' => true]);
    }
}
