<?php

// PhpWalletLib
// Copyright (C) 2019 Jittapan Pleumsumran

// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.

// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <https://www.gnu.org/licenses/>.

namespace secretdz\phpwalletlib;

use GuzzleHttp\Client;

class PhpWalletLib
{
    private $httpClient;
    private $token;

    /**
     * Send POST request to API endpoint
     * @return mixed GuzzleHttp response object
     */
    private function postJson($url, $data)
    {
        return $this->httpClient->request('POST', $url, [
            'json' => $data,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function __construct($username, $password, $type = 'email')
    {
        $this->httpClient = new Client([
            'base_uri' => 'https://mobile-api-gateway.truemoney.com/mobile-api-gateway/',
            'defaults' => [
                'headers' => [
                    [
                        'Host' => 'mobile-api-gateway.truemoney.com',
                        'User-Agent' => 'okhttp/3.8.0',
                    ],
                ],
            ],
        ]);

        $response = $this->postJson('api/v1/signin', [
            'username' => $username,
            'password' => sha1($username . $password),
            'type' => $type,
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new Exception('Authentication failed');
        } else {
            $body = @json_decode($response->getBody());
            $this->token = $body->data->accessToken;
        }
    }

    /**
     * Get current balance of this account
     * @return int balance of this account
     */
    public function GetBalance()
    {
        $response = $this->httpClient->request('GET', "api/v1/profile/balance/{$this->token}");
        if ($response->getStatusCode() !== 200) {
            throw new Exception('Failed to fetch balance');
        }
        return @json_decode($response->getBody())->data->currentBalance;
    }

    /**
     * Get profile of this account
     * @return mixed
     */
    public function GetProfile()
    {
        $response = $this->httpClient->request('GET', "api/v1/profile/{$this->token}");
        if ($response->getStatusCode() !== 200) {
            throw new Exception('Failed to fetch profile');
        }
        $profile = @json_decode($response->getBody(), true)['data'];
        // Just return what we get for now, too lazy to map it to my own format :>
        return $profile;
    }

    /**
     * Top up with TrueMoney cash card to this account.
     * @return bool
     */
    public function TopupCashCard($card)
    {
        $time = time();
        $response = $this->httpClient->request('POST', "api/v1/topup/mobile/$time/{$this->token}/cashcard/$card");
        return $response->getStatusCode() === 200;
    }

    /**
     * Get transaction for this account
     * @param start Start date in Y-m-d format
     * @param end End date in Y-m-d format
     * @param limit Maximum amount of transactions to fetch
     * @return mixed
     */
    public function GetPastTransactions($start, $end, $limit = 25)
    {
        $response = $this->httpClient->request('GET', "user-profile-composite/v1/users/transactions/history?start_date={$start}&end_date={$end}&limit={$limit}", [
            'headers' => [
                'Authorization' => $this->token,
            ],
        ]);
        if ($response->getStatusCode() !== 200) {
            throw new Exception('Failed to fetch transactions');
        }
        $body = @json_decode($response->getBody(), true)['data'];
        $transactions = [];
        foreach ($body['activities'] as $activity) {
            $transactions[] = new Transaction($activity);
        }

        $ret = [
            'total' => $body['total'],
            'currentPage' => $body['current_page'],
            'totalPage' => $body['total_page'],
            'limit' => $body['limit'],
            'transactions' => $transactions,
        ];

        return (object) $ret;
    }

    /**
     * Get details for a transaction
     * @param txid Transaction ID
     * @return mixed
     */
    public function GetTransactionDetails($txid)
    {
        $response = $this->httpClient->request('GET', "user-profile-composite/v1/users/transactions/history/detail/$txid", [
            'headers' => [
                'Authorization' => $this->token,
            ],
        ]);
        if ($response->getStatusCode() !== 200) {
            throw new Exception('Failed to fetch transaction details');
        }
        $body = @json_decode($response->getBody(), true)['data'];
        return $body;
    }

    public function Logout()
    {
        $response = $this->httpClient->request('POST', "api/v1/signout/{$this->token}");
        return $response->getStatusCode() === 200;
    }
}
