<?php
namespace VideoGamesRecords\CoreBundle\DataProvider;

use Google\Service\YouTube;
use Google\Client;
use Google\Service\YouTube\VideoListResponse;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class YoutubeProvider
{
    private Youtube $service;


    public function __construct(
        #[Autowire(env: 'string:GOOGLE_API_KEY')]
        private readonly string $apiKey
    )
    {
        $client = new Client();
        $client->addScope(YouTube::YOUTUBE);
        $client->setDeveloperKey($apiKey);

        $this->service = new YouTube($client);
    }

    /**
     * @param $videoId
     * @return VideoListResponse
     */
    public function getVideo($videoId): VideoListResponse
    {
        return $this->service->videos->listVideos('snippet,contentDetails,statistics', ['id' => $videoId]);
    }
}
