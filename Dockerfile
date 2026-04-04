FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev 

#clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

#install extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

#install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

#copy existing application directory contents
COPY . /var/www/html
RUN chown -R www-data:www-data /var/www/html   

EXPOSE 9000
CMD [ "php-fpm" ]
