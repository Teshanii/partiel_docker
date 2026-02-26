# Utilisation de l'image PHP avec Apache
FROM php:8.0-apache

# Installation de l'extension PDO pour MySQL (nécessaire pour le index.php)
RUN docker-php-ext-install pdo pdo_mysql

# On copie le code du dossier back vers le dossier d'Apache
COPY . /var/www/html/

 
EXPOSE 80