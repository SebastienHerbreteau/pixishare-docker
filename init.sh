#!/bin/bash

# Installez les dépendances via Composer
echo "Running composer install..."
composer install --no-interaction --optimize-autoloader

# Exécuter les migrations doctrine (si nécessaire)
echo "Running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction

# Nettoyage du cache Symfony
echo "Clear Symfony cache..."
php bin/console cache:clear

# Installer les dépendances Node.js
echo "Running npm install..."
npm install

# Exécuter une build NPM si nécessaire (c'est une étape standard dans des projets frontend avec Symfony Webpack)
echo "Running npm run build..."
npm run build

# Enfin, laissez le processus Apache tourner
echo "Starting Apache..."
exec apache2-foreground
