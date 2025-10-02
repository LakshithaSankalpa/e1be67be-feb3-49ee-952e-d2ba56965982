FROM php:8.1-cli

# Install system deps
RUN apt-get update -qq && apt-get install -y unzip git

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy project files
WORKDIR /app
COPY . .

# Install PHP deps
RUN composer install --no-dev

# For tests, we'll install dev deps in CI run
CMD ["echo", "Build complete"]
