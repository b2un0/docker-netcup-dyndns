<?php

declare(strict_types=1);

namespace netcup\DNS\API;

final class Config
{
    private string $apiKey;

    private string $apiPassword;

    private int $customerId;

    private string $domain;

    private string $mode;

    private int $ttl;

    private bool $force;

    public function __construct(string $domain, string $mode, int $customerId, string $apiKey, string $apiPassword, int $ttl, bool $force = false)
    {
        $this->domain = $domain;
        $this->mode = $mode;
        $this->customerId = $customerId;
        $this->apiKey = $apiKey;
        $this->apiPassword = $apiPassword;
        $this->ttl = $ttl;
        $this->force = $force;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getApiPassword(): string
    {
        return $this->apiPassword;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function getMatcher(): array
    {
        switch ($this->mode) {
            case 'both':
                return ['@', '*'];

            case '*':
                return ['*'];

            default:
                return ['@'];
        }
    }

    /**
     * there is no good way to get the correct "registrable" Domain without external libs!
     *
     * @see https://github.com/jeremykendall/php-domain-parser
     *
     * this method is still tricky, because:
     *
     * works: nas.tld.com
     * works: nas.tld.de
     * works: tld.com
     * failed: nas.tld.co.uk
     * failed: nas.home.tld.de
     */
    public function getHostname(): string
    {
        // hack if top level domain are used for dynDNS
        if (1 === substr_count($this->domain, '.')) {
            return $this->domain;
        }

        $domainParts = explode('.', $this->domain);
        array_shift($domainParts); // remove sub domain
        return implode('.', $domainParts);
    }

    public function getTtl(): int
    {
        return $this->ttl;
    }

    public function isForce(): bool
    {
        return $this->force;
    }
}