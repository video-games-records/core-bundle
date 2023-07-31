<?php
namespace VideoGamesRecords\CoreBundle\DataProvider;

use Google\Service\YouTube;
use Google\Client;
use Google\Service\YouTube\VideoListResponse;

class YoutubeProvider
{
    private Youtube $service;

    /**
     * @param string $apiKey
     */
    public function __construct(string $apiKey)
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
