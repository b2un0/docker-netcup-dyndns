<?php

declare(strict_types=1);

namespace netcup\DNS\API;

final class DynDNS
{
    private Config $config;

    private Client $client;

    private ?string $ipv4;
    private ?string $ipv6;

    public function __construct(Config $config, ?string $ipv4, ?string $ipv6)
    {
        $this->config = $config;

        $this->ipv4 = $ipv4;
        $this->ipv6 = $ipv6;

        $this->client = new Client(
            $config->getCustomerId(),
            $config->getApiKey(),
            $config->getApiPassword(),
        );

    }

    public function update(): void
    {
        $sid = $this->client->login();

        $this->doLog('api login successful');

        if ($ttl = $this->config->getTtl()) {
            $zone = $this->client->infoDnsZone($sid, $this->config->getDomain());

            if ($ttl !== (int)$zone->responsedata->ttl) {
                $zone->responsedata->ttl = $ttl;
                $this->client->updateDnsZone($sid, $this->config->getDomain(), $zone->responsedata);

                $this->doLog(sprintf('ttl for %s set to %s', $this->config->getDomain(), $ttl));
            } else {
                $this->doLog(sprintf('ttl for %s already set to %s', $this->config->getDomain(), $ttl));
            }
        }

        $records = $this->client->infoDnsRecords($sid, $this->config->getDomain());

        $changes = false;

        foreach ($records->responsedata->dnsrecords as $key => $record) {
            $recordHostnameReal = (!in_array($record->hostname, $this->config->getMatcher())) ? $record->hostname . '.' . $this->config->getHostname() : $this->config->getHostname();

            if ($recordHostnameReal === $this->config->getDomain()) {

                // update A Record if exists and IP has changed
                if ('A' === $record->type && $this->ipv4 &&
                    (
                        $this->config->isForce() ||
                        $record->destination !== $this->ipv4
                    )
                ) {
                    $record->destination = $this->ipv4;
                    $this->doLog(sprintf('IPv4 for %s set to %s', $record->hostname . '.' . $this->config->getHostname(), $this->ipv4));
                    $changes = true;
                }

                // update AAAA Record if exists and IP has changed
                if ('AAAA' === $record->type && $this->ipv6 &&
                    (
                        $this->config->isForce()
                        || $record->destination !== $this->ipv6
                    )
                ) {
                    $record->destination = $this->ipv6;
                    $this->doLog(sprintf('IPv6 for %s set to %s', $record->hostname . '.' . $this->config->getHostname(), $this->ipv6));
                    $changes = true;
                }
            }
        }

        if (true === $changes) {

            $this->client->updateDnsRecords(
                $sid, $this->config->getDomain(),
                $records->responsedata->dnsrecords
            );

            $this->doLog('dns recordset updated');
        } else {
            $this->doLog('dns recordset NOT updated (no changes)');
        }

        $this->client->logout($sid);

        $this->doLog('api logout successful');
    }

    private function doLog(string $msg)
    {
        printf('%s %s', $msg, PHP_EOL);
    }
}