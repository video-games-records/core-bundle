<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use VideoGamesRecords\CoreBundle\Entity\Team;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Repository\PlayerRepository;
use VideoGamesRecords\CoreBundle\Repository\TeamRepository;

/**
 * Class TeamController
 */
class TeamController extends AbstractController
{
    private TranslatorInterface $translator;
    private TeamRepository $teamRepository;
    private PlayerRepository $playerRepository;

    private array $extensions = array(
        'image/png' => '.png',
        'image/jpeg' => '.jpg',
    );

    public function __construct(TranslatorInterface $translator, TeamRepository $teamRepository, PlayerRepository $playerRepository)
    {
        $this->translator = $translator;
        $this->teamRepository = $teamRepository;
        $this->playerRepository = $playerRepository;
    }

    /**
     * @return Team|null
     * @throws NonUniqueResultException
     * @throws ORMException
     */
    private function getTeam(): ?Team
    {
        if ($this->getUser() !== null) {
            $player =  $this->playerRepository->getPlayerFromUser($this->getUser());
            return $player->getTeam();
        }
        return null;
    }

    /**
     * @return array
     * @throws NonUniqueResultException
     * @throws ORMException
     */
    public function rankingPointChart(): array
    {
        return $this->teamRepository->getRankingPointChart($this->getTeam());
    }

    /**
     * @return array
     * @throws NonUniqueResultException
     * @throws ORMException
     */
    public function rankingPointGame(): array
    {
        return $this->teamRepository->getRankingPointGame($this->getTeam());
    }

    /**
     * @return array
     * @throws NonUniqueResultException
     * @throws ORMException
     */
    public function rankingMedal(): array
    {
        return $this->teamRepository->getRankingMedal($this->getTeam());
    }

    /**
     * @return array
     * @throws NonUniqueResultException
     * @throws ORMException
     */
    public function rankingCup(): array
    {
        return $this->teamRepository->getRankingCup($this->getTeam());
    }

    /**
     * @return array
     * @throws NonUniqueResultException
     * @throws ORMException
     */
    public function rankingBadge() : array
    {
        return $this->teamRepository->getRankingBadge($this->getTeam());
    }

    /**
     * @return array
     */
    public function rankingPointGameTop5(): array
    {
        return $this->teamRepository->getRankingPointGame(null, 5);
    }

    /**
     * @return array
     */
    public function rankingCupTop5(): array
    {
        return $this->teamRepository->getRankingCup(null, 5);
    }

    /**
     * @param Request     $request
     * @return Response
     * @throws Exception
     */
    public function uploadAvatar(Request $request): Response
    {
        $team = $this->getTeam();
        $data = json_decode($request->getContent(), true);
        $file = $data['file'];
        $fp1 = fopen($file, 'r');
        $meta = stream_get_meta_data($fp1);

        $data = explode(',', $file);

        if (!array_key_exists($meta['mediatype'], $this->extensions)) {
            return $this->getResponse(false, $this->translator->trans('avatar.extension_not_allowed'));
        }

        $directory = $this->getParameter('videogamesrecords_core.directory.picture') . '/team';
        $filename = $team->getId() . '_' . uniqid() . $this->extensions[$meta['mediatype']];

        $fp2 = fopen($directory . '/' . $filename, 'w');
        fwrite($fp2, base64_decode($data[1]));
        fclose($fp2);
        // Save avatar

        $team->setLogo($filename);

        $this->teamRepository-flush();
        return $this->getResponse(true, $this->translator->trans('avatar.success'));
    }

    /**
     * @param bool $success
     * @param null    $message
     * @return Response
     */
    private function getResponse(bool $success, $message = null): Response
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode([
            'success' => $success,
            'message' => $message,
        ]));
        return $response;
    }
}
