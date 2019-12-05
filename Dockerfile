FROM php:7-cli-alpine

ENV SCHEDULE "*/10 * * * *"

ENV DOMAIN ""

ENV MODE "@"

ENV IPV4 "yes"
ENV IPV6 "no"

ENV TTL "0"

ENV CUSTOMER_ID ""
ENV API_KEY ""
ENV API_PASSWORD ""

ENV FORCE "no"

RUN mkdir /app/

ADD . /app/

RUN echo "@reboot php /app/updater.php" > /etc/crontabs/root && \
    echo "${SCHEDULE} php /app/updater.php" >> /etc/crontabs/root

CMD ["/usr/sbin/crond", "-l", "2", "-f"]

LABEL org.label-schema.name="Netcup DNS API DynDNS Docker Client" \
      org.label-schema.vcs-url="https://github.com/b2un0/docker-netcup-dyndns" \
      org.label-schema.schema-version="1.0"