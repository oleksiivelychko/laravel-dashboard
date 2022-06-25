FROM php:8.1-fpm

LABEL maintainer="Oleksii Velychko"

ARG CACHE_DIR="/tmp/cache"

ENV USER_ID 1000
ENV GROUP_ID 1000
ENV PHP_IDE_CONFIG="serverName=dockerHost"
ENV COMPOSER_HOME=${CACHE_DIR}/composer
ENV npm_config_cache=${CACHE_DIR}/npm
ENV NO_UPDATE_NOTIFIER=1

RUN echo 'root:root' | chpasswd

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    htop \
    git \
    openssl \
    libfreetype6-dev \
    libicu-dev \
    libonig-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libxslt1-dev \
    libxml2-dev \
    zlib1g-dev \
    libbz2-dev \
    libzip-dev \
    librabbitmq-dev \
    libssh-dev \
    libssl-dev \
    libcurl4-openssl-dev

RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/
RUN docker-php-ext-configure intl

RUN docker-php-ext-install \
    iconv \
    gd \
    intl \
    mbstring \
    mysqli \
    pdo \
    pdo_mysql \
    xsl \
    zip \
    soap \
    bcmath \
    bz2 \
    opcache \
    exif \
    sockets

RUN pecl install xdebug-3.1.5
RUN docker-php-ext-enable xdebug

RUN pecl install -o -f redis && rm -rf /tmp/pear && docker-php-ext-enable redis

RUN docker-php-source extract && \
    mkdir /usr/src/php/ext/amqp && \
    curl -L https://github.com/php-amqp/php-amqp/archive/master.tar.gz | tar -xzC /usr/src/php/ext/amqp --strip-components=1 && \
    docker-php-ext-install amqp && \
    docker-php-source delete

RUN cd /tmp && git clone https://github.com/openswoole/swoole-src.git && \
    cd swoole-src && \
    git checkout v4.8.1 && \
    phpize  && \
    ./configure --enable-openssl --enable-swoole-curl --enable-http2 --enable-mysqlnd && \
    make && make install
RUN echo 'extension=openswoole.so' > /usr/local/etc/php/conf.d/swoole.ini

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -L https://github.com/FriendsOfPHP/PHP-CS-Fixer/releases/download/v3.8.0/php-cs-fixer.phar > \
    /usr/local/bin/php-cs-fixer \
    && chmod +x /usr/local/bin/php-cs-fixer

RUN curl -sL https://deb.nodesource.com/setup_16.x | bash -
RUN apt-get install -y nodejs
RUN npm i -g npm-check-updates

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN mkdir -p ${CACHE_DIR}
RUN chown -R ${USER_ID}:${GROUP_ID} ${CACHE_DIR}

RUN usermod -u ${USER_ID} www-data && groupmod -g ${GROUP_ID} www-data
USER "${USER_ID}:${GROUP_ID}"

CMD ["php-fpm"]
