FROM php:5.5-apache

ENV DOCKERIZE_VERSION v0.7.0
RUN cat > /etc/apt/sources.list <<EOF
deb http://archive.debian.org/debian/ jessie main contrib non-free
deb-src http://archive.debian.org/debian/ jessie main contrib non-free
EOF
RUN apt-get update --allow-unauthenticated && \
    apt-get upgrade -y --allow-unauthenticated && \
    apt-get install -y --allow-unauthenticated \
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
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN curl -L --output /tmp/dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz https://github.com/jwilder/dockerize/releases/download/$DOCKERIZE_VERSION/dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz && \
    tar -C /usr/local/bin -xzvf /tmp/dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz && \
    rm /tmp/dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz

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
