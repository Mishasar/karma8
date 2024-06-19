FROM php:8.0.10-fpm-alpine

ARG GROUP_ID=1337
ARG USER_ID=1337

RUN addgroup -g $GROUP_ID -S dockeruser

RUN adduser -S -G dockeruser -u $USER_ID -s /bin/ash dockeruser

RUN apk add supervisor
RUN mkdir -p /var/log/supervisor
RUN mkdir -p /var/log/backend
RUN chown $USER_ID:$GROUP_ID /var/log/backend

RUN apk add zlib-dev
RUN apk add icu-dev

RUN docker-php-ext-install mysqli
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install bcmath

COPY --chown=$USER_ID:$GROUP_ID ./infra/php/php.ini /usr/local/etc/php/php.ini
COPY ./infra/php/supervisord.conf /etc/supervisord.conf

RUN apk add composer

WORKDIR /home/dockeruser

USER dockeruser

EXPOSE 9000

CMD ["/usr/bin/supervisord"]