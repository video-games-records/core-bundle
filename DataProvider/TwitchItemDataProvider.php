<?php
namespace VideoGamesRecords\CoreBundle\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\ResourceClassNotSupportedException;
use GuzzleHttp\Exception\GuzzleException;
use TwitchApi\HelixGuzzleClient;
use TwitchApi\TwitchApi;
use VideoGamesRecords\CoreBundle\Entity\Twitch;
use VideoGamesRecords\CoreBundle\Service\Rest\TwitchClient;

final class TwitchItemDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    private string $twitchClientId;
    private string $twitchClientSecret;
    private string $twitchAccessToken;
    private string $twitchBroadcasterId;
    private string $twitchScopes = '';
    private TwitchApi $twitchApi ;

    public function __construct($twitchClientId, $twitchClientSecret, $twitchBroadcasterId) {
        $this->twitchClientId = $twitchClientId;
        $this->twitchClientSecret = $twitchClientSecret;
        $this->twitchBroadcasterId = $twitchBroadcasterId;
        $helixGuzzleClient = new HelixGuzzleClient($this->twitchClientId);
        $this->twitchApi = new TwitchApi($helixGuzzleClient, $this->twitchClientId, $this->twitchClientSecret);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Twitch::class === $resourceClass;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $this->getAccessToken();

        return match ($context['item_operation_name']) {
            'twitch-channel-info' => $this->getChannelInfo(),
            'twitch-get-stream' => $this->getStream()
        };
    }

    private function getAccessToken(): void
    {
        $oauth = $this->twitchApi->getOauthApi();
        try {
            $token = $oauth->getAppAccessToken($this->twitchScopes ?? '');
            $data = json_decode($token->getBody()->getContents());

            // Your bearer token
            $this->twitchAccessToken = $data->access_token ?? null;
        } catch (Exception $e) {
            //TODO: Handle Error
        } catch (GuzzleException $e) {

        }
    }

    private function getChannelInfo()
    {
        $response = $this->twitchApi->getChannelsApi()->getChannelInfo($this->twitchAccessToken, $this->twitchBroadcasterId);
        return json_decode($response->getBody()->getContents());
    }

    private function getStream()
    {
        $response = $this->twitchApi->getStreamsApi()->getStreamForUsername($this->twitchAccessToken, 'videogamesrecords');
        return json_decode($response->getBody()->getContents());
    }
}
