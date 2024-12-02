FROM php:apache-buster

# Install required build tools and libraries
RUN apt-get update && apt-get install -y \
    build-essential \
    default-mysql-client \
    libmariadb-dev \
    && docker-php-ext-install mysqli \
    && apt-get clean
