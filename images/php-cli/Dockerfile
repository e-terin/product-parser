ARG PHP_VERSION=8.1
FROM composer:latest AS composer
FROM php:${PHP_VERSION}-cli

# copy composer from Composer image to PHP image
COPY --from=composer /usr/bin/composer /usr/bin/composer

ARG USER_ID=1000
ARG GROUP_ID=1000
ARG USER_NAME='profkolor'
ARG TIMEZONE='Europe/Moscow'

RUN apt-get update && apt-get install -y wget unzip libzip-dev

# Расширение GD
#RUN apt-get update && apt-get install -y \
#		libfreetype6-dev \
#		libjpeg62-turbo-dev \
#		libpng-dev \
#	&& docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
#	&& docker-php-ext-install -j$(nproc) gd

RUN apt-get update && apt-get install -y libmagickwand-dev --no-install-recommends && rm -rf /var/lib/apt/lists/*
#
## install imagick
## Version is not officially released https://pecl.php.net/get/imagick but following works for PHP 8
#RUN mkdir -p /usr/src/php/ext/imagick; \
#    curl -fsSL https://github.com/Imagick/imagick/archive/06116aa24b76edaf6b1693198f79e6c295eda8a9.tar.gz | tar xvz -C "/usr/src/php/ext/imagick" --strip 1; \
#    docker-php-ext-install imagick;

# Расширения PECL: xdebug и imagick
RUN pecl install xdebug-3.1.2 && docker-php-ext-enable xdebug
RUN pecl install imagick-3.6.0 && docker-php-ext-enable imagick

# Стандартные расширения PHP
RUN docker-php-ext-install bcmath
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN docker-php-ext-install zip
RUN docker-php-ext-install pdo_mysql && docker-php-ext-enable pdo_mysql

ADD "my-php.ini" "/usr/local/etc/php/conf.d/my-php.ini"

#ENV TZ=${TIMEZONE}
#ENV XDEBUG_CONFIG="client_host=172.16.20.1 client_port=9003 start_with_request=yes"
#ENV PHP_IDE_CONFIG="serverName=Profkolor"

#RUN useradd -M ${USER_NAME}
#RUN usermod -u ${USER_ID} ${USER_NAME} && groupmod -g ${GROUP_ID} ${USER_NAME}
#RUN mkdir /app
#RUN chown -R ${USER_ID}:${GROUP_ID} /app
#USER "${USER_ID}:${GROUP_ID}"
#WORKDIR /app

#CMD ["php","-v"]

