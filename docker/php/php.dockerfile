FROM php:8.4-fpm

WORKDIR '/var/www'

RUN apt-get update && apt-get install -y \
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
    gawk \
    libaio1t64 \
    alien \
    wget \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

ENV TZ=America/Sao_Paulo

# ─── Oracle Instant Client ────────────────────────────────────────────────────
RUN wget -q https://download.oracle.com/otn_software/linux/instantclient/2113000/oracle-instantclient-basic-21.13.0.0.0-1.x86_64.rpm \
    && wget -q https://download.oracle.com/otn_software/linux/instantclient/2113000/oracle-instantclient-devel-21.13.0.0.0-1.x86_64.rpm \
    && alien --to-deb --scripts oracle-instantclient-basic-21.13.0.0.0-1.x86_64.rpm \
    && alien --to-deb --scripts oracle-instantclient-devel-21.13.0.0.0-1.x86_64.rpm \
    && dpkg -i oracle-instantclient-basic_21.13.0.0.0-2_amd64.deb \
    && dpkg -i oracle-instantclient-devel_21.13.0.0.0-2_amd64.deb \
    && rm -f *.rpm *.deb \
    && ln -sf /usr/lib/x86_64-linux-gnu/libaio.so.1t64 /usr/lib/x86_64-linux-gnu/libaio.so.1

ENV LD_LIBRARY_PATH="/usr/lib/oracle/21/client64/lib:${LD_LIBRARY_PATH}"
ENV ORACLE_HOME="/usr/lib/oracle/21/client64"

RUN echo "/usr/lib/oracle/21/client64/lib" > /etc/ld.so.conf.d/oracle.conf && ldconfig

# ─── Extensões PHP ────────────────────────────────────────────────────────────
RUN docker-php-ext-install pdo pdo_pgsql mbstring zip

# OCI8
RUN echo "instantclient,/usr/lib/oracle/21/client64/lib" | pecl install oci8 \
    && docker-php-ext-enable oci8

# PDO_OCI  ← corrigido
RUN echo "instantclient,/usr/lib/oracle/21/client64/lib" | pecl install pdo_oci \
    && docker-php-ext-enable pdo_oci

# ─── Composer ────────────────────────────────────────────────────────────────
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN groupadd -g 1000 www \
    && useradd -u 1000 -ms /bin/bash -g www www

COPY src /var/www

RUN chown -R www:www /var/www

USER www

EXPOSE 9000
CMD ["php-fpm"]