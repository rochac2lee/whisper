#!/bin/bash

# Script de inicialização do container

# Aguardar o banco de dados estar pronto
echo "Aguardando o banco de dados..."
until php artisan migrate:status > /dev/null 2>&1; do
    echo "Banco de dados não está pronto - aguardando..."
    sleep 2
done

echo "Banco de dados está pronto!"

# Executar migrations
echo "Executando migrations..."
php artisan migrate --force

# Executar seeds
echo "Executando seeds..."
php artisan db:seed --force

# Criar link simbólico para storage
echo "Criando link de storage..."
php artisan storage:link

# Criar diretório para anexos
echo "Criando diretórios necessários..."
mkdir -p storage/app/private/attachments
chmod -R 775 storage bootstrap/cache

echo "Inicialização completa!"

# Iniciar Apache
apache2-foreground

