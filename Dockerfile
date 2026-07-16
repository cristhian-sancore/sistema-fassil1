FROM php:7.4-apache

# Atualiza os pacotes e instala dependências do sistema e extensões do PHP necessárias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    unzip \
    curl \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd mysqli pdo_mysql mbstring zip opcache

# Habilita o módulo mod_rewrite do Apache e define ServerName para suprimir o aviso AH00558
RUN a2enmod rewrite headers \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Altera o DocumentRoot do Apache para a pasta /var/www/html/public_html
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public_html
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Configura o PHP para ignorar avisos de depreciação e notices (comum ao rodar código legado no PHP 7.4)
RUN echo "error_reporting = E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_STRICT\n" \
         "display_errors = On\n" \
         "max_execution_time = 300\n" \
         "memory_limit = 512M\n" \
         "post_max_size = 64M\n" \
         "upload_max_filesize = 64M\n" \
         "session.save_path = \"/tmp/sessions\"\n" > /usr/local/etc/php/conf.d/custom.ini

# Define a pasta de trabalho
WORKDIR /var/www/html/public_html

# Copia todos os arquivos do site e painel administrativo para a imagem
COPY ./public_html /var/www/html/public_html

# Concede permissões nas pastas de cache do Smarty e sessões do PHP
RUN mkdir -p /var/www/html/public_html/templates_c /var/www/html/public_html/cache /tmp/sessions \
    && chown -R www-data:www-data /var/www/html /tmp/sessions \
    && chmod -R 777 /var/www/html/public_html/templates_c /var/www/html/public_html/cache /tmp/sessions

EXPOSE 80
