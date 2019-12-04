<?php

declare(strict_types=1);

namespace netcup\DNS\API;

final class Client
{
    private const APIURL = 'https://ccp.netcup.net/run/webservice/servers/endpoint.php?JSON';

    private int $customerId;

    private string $apiKey;

    private string $apiPassword;

    public function __construct(int $customerId, string $apiKey, string $apiPassword)
    {
        $this->customerId = $customerId;
        $this->apiKey = $apiKey;
        $this->apiPassword = $apiPassword;
    }

    public function login(): string
    {
        $request = [
            'action' => 'login',
            'param' =>
                [
                    'customernumber' => $this->customerId,
                    'apikey' => $this->apiKey,
                    'apipassword' => $this->apiPassword,
                ],
        ];

        return $this->request($request)->responsedata->apisessionid;
    }

    public function logout(string $sid): void
    {
        $request = [
            'action' => 'logout',
            'param' =>
                [
                    'customernumber' => $this->customerId,
                    'apikey' => $this->apiKey,
                    'apisessionid' => $sid,
                ],
        ];

        $this->request($request);
    }

    public function infoDnsRecords(string $sid, string $domainname): object
    {
        $request = [
            'action' => 'infoDnsRecords',
            'param' =>
                [
                    'domainname' => $domainname,
                    'customernumber' => $this->customerId,
                    'apikey' => $this->apiKey,
                    'apisessionid' => $sid,
                ],
        ];

        return $this->request($request);
    }

    public function updateDnsRecords(string $sid, string $domainname, array $record): object
    {
        $request = [
            'action' => 'updateDnsRecords',
            'param' =>
                [
                    'domainname' => $domainname,
                    'customernumber' => $this->customerId,
                    'apikey' => $this->apiKey,
                    'apisessionid' => $sid,
                    'dnsrecordset' => [
                        'dnsrecords' => $record,
                    ],
                ],
        ];

        return $this->request($request);
    }

    public function infoDnsZone(string $sid, string $domainname): object
    {
        $request = [
            'action' => 'infoDnsZone',
            'param' =>
                [
                    'domainname' => $domainname,
                    'customernumber' => $this->customerId,
                    'apikey' => $this->apiKey,
                    'apisessionid' => $sid,
                ],
        ];

        return $this->request($request);
    }

    public function updateDnsZone(string $sid, string $domainname, object $zone): object
    {
        $request = [
            'action' => 'updateDnsZone',
            'param' =>
                [
                    'domainname' => $domainname,
                    'customernumber' => $this->customerId,
                    'apikey' => $this->apiKey,
                    'apisessionid' => $sid,
                    'dnszone' => $zone,
                ],
        ];

        return $this->request($request);
    }

    private function request(array $request): ?object
    {
        $ch = curl_init(self::APIURL);

        $curlOptions = [
            CURLOPT_POST => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode($request, JSON_THROW_ON_ERROR),
        ];

        curl_setopt_array($ch, $curlOptions);

        $result = curl_exec($ch);

        curl_close($ch);

        $response = json_decode($result, false, 512, JSON_THROW_ON_ERROR);

        if (2000 !== $response->statuscode) {
            throw new \RuntimeException($response->longmessage);
        }

        return $response;
    }
}