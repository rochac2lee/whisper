FROM php:8.2-apache

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev

# Limpar cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensões PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Habilitar mod_rewrite do Apache
RUN a2enmod rewrite

# Copiar configuração do Apache
COPY .docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Configurar usuário
RUN useradd -G www-data,root -u 1000 -d /home/whisper whisper
RUN mkdir -p /home/whisper/.composer && \
    chown -R whisper:whisper /home/whisper

# Definir diretório de trabalho
WORKDIR /var/www

# Copiar arquivos de dependências e instalar vendor
COPY --chown=www-data:www-data composer.json composer.lock /var/www/
RUN composer install --prefer-dist --no-interaction --no-progress --no-scripts

# Copiar arquivos existentes
COPY --chown=www-data:www-data . /var/www

# Executar scripts do Composer após copiar o código
RUN composer install --prefer-dist --no-interaction --no-progress

# Configurar permissões
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/vendor

EXPOSE 80

CMD ["apache2-foreground"]
