# Usa a imagem oficial do PHP com Apache
FROM php:8.2-apache

# Habilita mod_rewrite do Apache
RUN a2enmod rewrite

# Instala e habilita extensões do SQLite
RUN apt-get update && apt-get install -y \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite sqlite3 \
    && apt-get clean

# Cria diretório para o banco SQLite e dá permissões
RUN mkdir -p /var/www/database \
    && chown -R www-data:www-data /var/www/database \
    && chmod 755 /var/www/database

# Copia o código da aplicação
COPY src/ /var/www/html/

# Altera as permissões
RUN chown -R www-data:www-data /var/www/html
