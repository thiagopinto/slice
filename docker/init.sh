#!/bin/bash

set -e

echo "🔧 Criando pasta de dados do MySQL em docker/data/mysql..."

mkdir -p "$(dirname "$0")/data/mysql"

echo "🔐 Ajustando permissões..."

# UID 999 é o padrão do MySQL oficial. Você pode adaptar se necessário.
sudo chown -R 999:999 "$(dirname "$0")/data/mysql"

echo "✅ Pasta de dados pronta!"
