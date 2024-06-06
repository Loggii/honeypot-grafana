FROM php:cli

RUN apt update && \
    apt install git -y && \
    docker-php-ext-install mysqli

RUN mkdir /app
WORKDIR /app

COPY sync .
COPY geo ./geo/
RUN php composer.phar install