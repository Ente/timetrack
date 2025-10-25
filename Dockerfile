# ---- Base image ------------------------------------------------------------
FROM php:8.2-apache

# ---- System packages & PHP extensions -------------------------------------
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    libgd-dev \
    libgmp-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    libxslt1-dev \
    libldap2-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    locales \
 && docker-php-ext-configure intl \
 && docker-php-ext-configure gd --with-jpeg --with-freetype \
 && docker-php-ext-install \
      curl \
      gd \
      gmp \
      intl \
      mbstring \
      mysqli \
      pdo \
      pdo_mysql \
      xsl \
      gettext \
      dom \
      ldap \
      zip \
 && a2enmod rewrite

RUN apt-get update && apt-get install -y default-mysql-client


 # ---- Add Python and smartcard support --------------------------------------
RUN apt-get update && apt-get install -y \
    python3 \
    python3-pip \
    pcscd \
    pcsc-tools \
    libpcsclite-dev \
 && pip3 install pyscard \
 && systemctl enable pcscd || true \
&& rm -rf /var/lib/apt/lists/*


RUN { \
      echo "display_errors = Off"; \
      echo "display_startup_errors = Off"; \
      echo "log_errors = On"; \
      echo "error_reporting = E_ALL & ~E_DEPRECATED & ~E_WARNING & ~E_NOTICE"; \
      echo "error_log = /var/log/php_errors.log"; \
    } > /usr/local/etc/php/conf.d/99-production.ini


# ---- Locale configuration --------------------------------------------------
RUN sed -i '/en_US.UTF-8/s/^# //g' /etc/locale.gen && locale-gen

# ---- Working directory -----------------------------------------------------
WORKDIR /var/www/html

# ---- Copy application files ------------------------------------------------
COPY . /var/www/html

# ---- Permissions -----------------------------------------------------------
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html

# ---- Apache vHost ----------------------------------------------------------
COPY docker/apache2.conf /etc/apache2/sites-available/000-default.conf

# ---- Composer --------------------------------------------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --no-interaction --optimize-autoloader

# ---- Entry point -----------------------------------------------------------
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80
ENTRYPOINT ["/entrypoint.sh"]
