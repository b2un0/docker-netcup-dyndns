# Netcup DNS API DynDNS Docker Client

![Docker Pulls](https://img.shields.io/docker/pulls/b2un0/netcup-dyndns.svg)
![Docker Build](https://github.com/b2un0/docker-netcup-dyndns/workflows/container/badge.svg?branch=main&event=push)
![MicroBadger Size](https://img.shields.io/docker/image-size/b2un0/netcup-dyndns.svg)

## Credits
based on business logic from:
- https://github.com/fernwerker/ownDynDNS
- https://github.com/stecklars/dynamic-dns-netcup-api

This container uses the official [PHP image](https://hub.docker.com/_/php/) as a base image (cli-alpine)

## pre requirements
* Create each host record in your netcup CCP before using the script. The script does not create any missing records!

## run as docker container

via `docker-compose.yml` on your NAS
````yaml
version: '3'

services:
    netcup-dyndns:
        image: b2un0/netcup-dyndns:latest
        restart: unless-stopped
        container_name: netcup-dyndns
        network_mode: host # necessary for ipv6!
        environment:
            SCHEDULE: "*/10 * * * *" # https://crontab.guru/
            DOMAIN: "nas.domain.tld"
            MODE: "both" # can be "@", "*" or "both"
            IPV4: "yes"
            IPV6: "yes"
            TTL: "300" # 0 or remove if zone ttl should not change
            # IPV4_URL: "https://api.ipify.org"   # optional, default: http://v4.ident.me
            # IPV6_URL: "https://api6.ipify.org"  # optional, default: http://v6.ident.me
            CUSTOMER_ID: "<customerId>"
            API_KEY: "<apiKey>"
            API_PASSWORD: "<apiPassword>"
````

### with Docker Secrets

Sensitive values (`CUSTOMER_ID`, `API_KEY`, `API_PASSWORD`) can be passed via [Docker secrets](https://docs.docker.com/compose/how-tos/use-secrets/) instead of plain environment variables.  
Use the `<VAR>_FILE` variant to point to the secret file:

````yaml
version: '3'

secrets:
    netcup_customer_id:
        file: ./secrets/customer_id.txt
    netcup_api_key:
        file: ./secrets/api_key.txt
    netcup_api_password:
        file: ./secrets/api_password.txt

services:
    netcup-dyndns:
        image: b2un0/netcup-dyndns:latest
        restart: unless-stopped
        container_name: netcup-dyndns
        network_mode: host # necessary for ipv6!
        secrets:
            - netcup_customer_id
            - netcup_api_key
            - netcup_api_password
        environment:
            SCHEDULE: "*/10 * * * *" # https://crontab.guru/
            DOMAIN: "nas.domain.tld"
            MODE: "both" # can be "@", "*" or "both"
            IPV4: "yes"
            IPV6: "yes"
            TTL: "300" # 0 or remove if zone ttl should not change
            # IPV4_URL: "https://api.ipify.org"   # optional, default: http://v4.ident.me
            # IPV6_URL: "https://api6.ipify.org"  # optional, default: http://v6.ident.me
            CUSTOMER_ID_FILE: /run/secrets/netcup_customer_id
            API_KEY_FILE: /run/secrets/netcup_api_key
            API_PASSWORD_FILE: /run/secrets/netcup_api_password
````

## run without docker

via `wrapper.php` (or some other script name)
```
<?php

$_ENV['DOMAIN'] = 'nas.domain.tld';
$_ENV['MODE'] = 'both';  # can be "@", "*" or "both"

$_ENV['CUSTOMER_ID'] = '<customerId>';
$_ENV['API_KEY'] = '<apiKey>';
$_ENV['API_PASSWORD'] = '<apiPassword>';

$_ENV['TTL'] = 300; # 0 or remove if zone ttl should not change

$_ENV['IPV4'] = 'yes';
$_ENV['IPV6'] = 'no';

# $_ENV['IPV4_URL'] = 'https://api.ipify.org';  // optional, default: http://v4.ident.me
# $_ENV['IPV6_URL'] = 'https://api6.ipify.org'; // optional, default: http://v6.ident.me

$_ENV['FORCE'] = 'no';

require 'updater.php';
```

## References
* DNS API Documentation: https://ccp.netcup.net/run/webservice/servers/endpoint.php
* Default IPv4 lookup: http://v4.ident.me – alternatives: https://api.ipify.org
* Default IPv6 lookup: http://v6.ident.me – alternatives: https://api6.ipify.org

## License
Published under GNU General Public License v3.0  

