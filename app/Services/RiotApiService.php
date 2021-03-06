<?php
/**
 * Created by PhpStorm.
 * User: guilh
 * Date: 02/06/2019
 * Time: 17:25
 */

namespace App\Services;

use GuzzleHttp\Client;

class RiotApiService
{
    private $apiKey;
    private $client;
    private $leagueClient;

    public function __construct()
    {
        $this->apiKey = env('RIOT_API_KEY');
        $this->client = new Client(['base_uri' => 'https://br1.api.riotgames.com/lol/summoner/v4/']);
        $this->leagueClient = new Client(['base_uri' => 'https://br1.api.riotgames.com/lol/league/v4/']);
    }

    public function summonerByName($summonerName)
    {
        $res = $this->client->request('GET',"summoners/by-name/$summonerName", [
                    'query' => ['api_key' => $this->apiKey]
                ]);

        return json_decode($res->getBody());
    }

    public function summonerByAccountId($accountId)
    {
        $res = $this->client->request('GET',"summoners/by-account/$accountId", [
            'query' => ['api_key' => $this->apiKey]
        ]);

        return json_decode($res->getBody());
    }

    public function rankingsOf($summonerId)
    {
        $res = $this->leagueClient->request('GET',"entries/by-summoner/$summonerId", [
            'query' => ['api_key' => $this->apiKey]
        ]);

        $entries = json_decode($res->getBody());

        return array_map(function($item){
            return [
                "queueType" => $item->queueType,
                "tier" => $item->tier,
                "rank" => $item->rank,
            ];
        }, $entries);
    }
}
