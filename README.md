# Netcup DNS API DynDNS Docker Client  

based on business logic from:
- https://github.com/fernwerker/ownDynDNS
- https://github.com/stecklars/dynamic-dns-netcup-api

## pre requirements
* Create each host record in your netcup CCP before using the script. The script does not create any missing records!

## run as docker container

via `docker-compose.yml` on your nas
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
            CUSTOMER_ID: "<customerId>"
            API_KEY: "<apiKey>"
            API_PASSWORD: "<apiPassword>"
````

## References
* DNS API Documentation: https://ccp.netcup.net/run/webservice/servers/endpoint.php
* Source of dnsapi.php: https://ccp.netcup.net/run/webservice/servers/endpoint.php?PHPSOAPCLIENT

## License
Published under GNU General Public License v3.0  

