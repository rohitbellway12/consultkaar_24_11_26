<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class AgoraService
{
    protected $appId;
    protected $appCertificate;
    protected $baseUrl = 'https://api.agora.io/';

    public function __construct($appId, $appCertificate)
    {
        $this->appId = "98afcf84bd645dd80a27d28b47ccba3";
        $this->appCertificate = "342a529afa3b402495b707c754cad29a";
    }

    public function getChannelParticipantCount($channelName)
    {
        $response = Http::get($this->baseUrl . 'v1/channel/' . $channelName . '/users', [
            'appId' => $this->appId,
            'appCertificate' => $this->appCertificate,
        ]);

        return count($response->json()['users']);
    }
}