<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use VideoGamesRecords\CoreBundle\Entity\Team;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TeamController
 * @Route("/team")
 */
class TeamController extends AbstractController
{
    private $translator;

    private $extensions = array(
        'image/png' => '.png',
        'image/jpeg' => '.jpg',
    );

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return Team|null
     */
    private function getTeam()
    {
        if ($this->getUser() !== null) {
            $player =  $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')
                ->getPlayerFromUser($this->getUser());
            return $player->getTeam();
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function rankingPointChart()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Team')->getRankingPointChart($this->getTeam());
    }

    /**
     * @return mixed
     */
    public function rankingPointGame()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Team')->getRankingPointGame($this->getTeam());
    }

    /**
     * @return mixed
     */
    public function rankingMedal()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Team')->getRankingMedal($this->getTeam());
    }

    /**
     * @return mixed
     */
    public function rankingCup()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Team')->getRankingCup($this->getTeam());
    }

    /**
     * @return mixed
     */
    public function rankingBadge()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Team')->getRankingBadge($this->getTeam());
    }

    /**
     * @return mixed
     */
    public function rankingPointGameTop5()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Team')->getRankingPointGame(null, 5);
    }

    /**
     * @return mixed
     */
    public function rankingCupTop5()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Team')->getRankingCup(null, 5);
    }

    /**
     * @param Request     $request
     * @return Response
     * @throws Exception
     */
    public function uploadAvatar(Request $request)
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

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->getResponse(true, $this->translator->trans('avatar.success'));
    }

    /**
     * @param bool $success
     * @param null    $message
     * @return Response
     */
    private function getResponse(bool $success, $message = null)
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
