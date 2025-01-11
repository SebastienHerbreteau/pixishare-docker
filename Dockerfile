FROM php:8.2-apache

# Installer les paquets nécessaires et extensions PHP
RUN apt-get update && apt-get install -y \
    vim \
    zlib1g-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libffi-dev \
    libwebp-dev \
    curl \
    unzip \
    default-mysql-client \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install \
    gd \
    mbstring \
    mysqli \
    pdo \
    pdo_mysql \
    opcache \
    calendar \
    ctype \
    exif \
    ffi \
    && docker-php-ext-enable \
    gd \
    mbstring \
    mysqli \
    pdo \
    pdo_mysql \
    opcache \
    calendar \
    ctype \
    exif \
    ffi \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

RUN a2enmod rewrite

WORKDIR /var/www/

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl https://get.symfony.com/cli/installer | bash

RUN mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

COPY default.conf /etc/apache2/sites-enabled/000-default.conf
COPY init.sh /init.sh

RUN chmod +x /init.sh

RUN rm -rf /var/lib/apt/lists/* && \
    apt-get clean

# Exposer le port 80
EXPOSE 80

ENTRYPOINT ["/init.sh"]