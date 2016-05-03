FROM php:5.5-apache  
 
RUN apt-get update  
RUN apt-get install -y libmcrypt-dev libfreetype6-dev libjpeg-dev libpng12-dev php5-mysql php5-mcrypt php5-curl php5-imap php5-gd  
 
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/  
RUN docker-php-ext-install -j$(nproc) gd  
RUN docker-php-ext-install -j$(nproc) mcrypt  
RUN docker-php-ext-install -j$(nproc) mysql   
RUN docker-php-ext-install -j$(nproc) mysqli

RUN a2enmod rewrite