FROM php:8.2-apache

# Instalar extensión mysqli para conectar con MySQL/Aiven
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Copiar tus archivos al servidor web
COPY . /var/www/html/

# Exponer el puerto 80
EXPOSE 80