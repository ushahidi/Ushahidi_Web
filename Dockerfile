FROM php:5.5-apache

ENV DOCKERIZE_VERSION v0.6.1
RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y \
      wget \
      libfreetype6-dev \
      libjpeg62-turbo-dev \
      libpng-dev \
      libmcrypt-dev \
      libc-client2007e-dev \
      libkrb5-dev \
      libcurl4-openssl-dev \
      libzip-dev \
      gettext-base \
      unzip \
      rsync \
      git \
      bison \
      netcat && \
    docker-php-ext-install mcrypt bcmath pdo_mysql mysqli zip && \
    docker-php-ext-configure imap --with-kerberos --with-imap-ssl && \
    docker-php-ext-install imap && \
    docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ && \
    docker-php-ext-install gd && \
    wget https://github.com/jwilder/dockerize/releases/download/$DOCKERIZE_VERSION/dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz && \
    tar -C /usr/local/bin -xzvf dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz && \
    rm dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* 

WORKDIR /var/www/html/
COPY ./ /var/www/html/
RUN chmod -R 777 application/config && \
  chmod -R 777 application/cache && \
  chmod -R 777 application/logs && \
  chmod -R 777 media/uploads && \
  chmod 644 .htaccess

COPY docker/entrypoint.sh /entrypoint.sh

ENTRYPOINT [ "/bin/bash", "/entrypoint.sh" ]
CMD [ "apache2-foreground" ]
