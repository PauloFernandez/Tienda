FROM php:8.3-fpm

# Argumentos para UID/GID
ARG UID=1000
ARG GID=1000

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y libicu-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    git \
    unzip \
    curl \
    vim \
    build-essential \
    libonig-dev \
    libxml2-dev \
    zip \
    sudo \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure intl \
    && docker-php-ext-install gd zip pdo pdo_mysql bcmath \
    && docker-php-ext-install intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Node.js LTS (versión 20)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Crear usuario con permisos de sudo para desarrollo
RUN groupadd --gid ${GID} laravel && \
    useradd --uid ${UID} --gid laravel --create-home --shell /bin/bash laravel && \
    echo "laravel ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Dar permisos al usuario laravel
RUN chown -R laravel:laravel /var/www/html

# CAMBIAR AL USUARIO LARAVEL
USER laravel

# Exponer puertos de PHP-FPM y Vite
EXPOSE 9000 5173

# Ejecutar PHP-FPM cuando el contenedor se inicie
CMD ["php-fpm"]
