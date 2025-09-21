# Gunakan image resmi PHP dengan server Apache
FROM php:8.2-apache

# Install ekstensi mysqli yang dibutuhkan untuk koneksi ke database
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli