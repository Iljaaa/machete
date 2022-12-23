FROM php:7.4-fpm

RUN apt-get update

# php-mysql
# RUN docker-php-ext-install pdo pdo_mysql mysqli

# redis & xdebug
# https://blog.denisbondar.com/post/phpstorm_docker_xdebug
#RUN pecl install redis \
#    && docker-php-ext-enable redis \
RUN pecl install xdebug-3.1.4 \
    && docker-php-ext-enable xdebug

ADD ./php.ini /usr/local/etc/php/php.ini

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet \
    && rm -rf /root/.composer/cache

WORKDIR /composer-package
