# Use a imagem oficial do PHP
FROM php:8.4-fpm

# Define o diretório de trabalho
WORKDIR '/var/www'

# Instale as dependências
RUN apt-get update && apt-get install -y  \
    iputils-ping \
    netcat-openbsd \
    net-tools \
    build-essential \
    locales \
    tzdata \
    nano \
    curl \
    libonig-dev \
    libpq-dev \
    libpng-dev \
    unzip \
    git \
    libzip-dev \
    gawk

ENV TZ=America/Sao_Paulo

# Limpe o cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instale as extensões
RUN docker-php-ext-install pdo pdo_pgsql mbstring zip

# Instale o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Usuário da aplicação
RUN groupadd -g 1000 www \
    && useradd -u 1000 -ms /bin/bash -g www www

# Copie o conteúdo do diretório da aplicação existente
COPY src /var/www

# Permissões
RUN chown -R www:www /var/www

# Altere o usuário atual para www
USER www

# Exponha a porta 9000 e inicie o servidor php-fpm
EXPOSE 9000
CMD ["php-fpm"]