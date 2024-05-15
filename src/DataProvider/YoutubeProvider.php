<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\DataProvider;

use Google\Service\Exception;
use Google\Service\YouTube;
use Google\Client;
use Google\Service\YouTube\VideoListResponse;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class YoutubeProvider
{
    public function __construct(
        #[Autowire(env: 'string:GOOGLE_API_KEY')]
        private readonly string $apiKey
    ) {
    }

    /**
     * @param $videoId
     * @return VideoListResponse
     * @throws Exception
     */
    public function getVideo($videoId): VideoListResponse
    {
        $client = new Client();
        $client->addScope(YouTube::YOUTUBE);
        $client->setDeveloperKey($this->apiKey);
        $service = new YouTube($client);
        return $service->videos->listVideos('snippet,contentDetails,statistics', ['id' => $videoId]);
    }
}
