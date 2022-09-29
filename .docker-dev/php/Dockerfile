FROM php:8.1.11RC1-fpm-buster
WORKDIR "/sources"

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

ARG DOCKER_USER
ARG DOCKER_UID
ARG DOCKER_GID
RUN userdel -f www-data
RUN if getent group www-data ; then groupdel www-data; fi
RUN groupadd -g ${DOCKER_GID} ${DOCKER_USER}
RUN useradd -l -u ${DOCKER_UID} -g ${DOCKER_USER} ${DOCKER_USER}
RUN install -d -m 0755 -o ${DOCKER_USER} -g ${DOCKER_USER} /home/${DOCKER_USER}

RUN apt-get update && apt-get -y --no-install-recommends install \
    libjpeg62-turbo-dev \
    libpng-dev \
    libfreetype6-dev \
    libssl-dev \
    libxml2-dev \
    zlib1g-dev \
    libicu-dev \
    libzip-dev \
    git

RUN docker-php-ext-configure pcntl --enable-pcntl \
    && docker-php-ext-configure intl \
    && docker-php-ext-install \
        pcntl \
        intl \
    && docker-php-ext-configure gd \
        --with-jpeg=/usr/include/ \
        --with-freetype=/usr/include/ \
    && docker-php-ext-install \
        gd \
        iconv \
        opcache \
    && docker-php-ext-enable opcache

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php --install-dir=/bin --filename=composer
RUN php -r "unlink('composer-setup.php');"

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash
RUN apt-get -y --no-install-recommends install symfony-cli

RUN apt-get clean -y && rm -rf /var/lib/apt/lists/*

USER ${DOCKER_USER}
