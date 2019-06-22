FROM php:7.1.23

WORKDIR /app

COPY . .

RUN apt-get update -y && apt-get install -y openssl zip unzip git \
&& curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
&& docker-php-ext-install pdo mbstring mysqli pdo_mysql \
&& composer install \
&& php artisan cache:clear

CMD sh ./run.sh

EXPOSE 80
