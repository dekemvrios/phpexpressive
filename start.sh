#!/bin/sh

# Endereço de ip utilizado na definição da sessão do XDEBUG.

ip_addres=$(eval hostname -I | awk '{print $1}')

# Especifica a instalação do Debugger. Deve permanecer desativado caso necessário validação de performance
# de execução da biblioteca.

use_xdebug=false

# Configurações da conexão com a base de dados utilizada pelo ORM.

# database configuration {
    # Driver utilizado para conexão
    db_driver=??

    # Nome da base de dados
    db_name=??

    # Usuário com privilégios de escrita
    db_user=??

    # Senha do respectivo usuário
    db_pass=??

    # Host qual contém a base de dados
    db_host=??
# }

# Inicialização do container docker de acordo com as definições anteriores.

IP=$ip_addres \
INSTALL_XDEBUG=$use_xdebug \
DB_DRIVER=$db_driver \
DB_HOST=$db_host \
DB_NAME=$db_name \
DB_USER=$db_user \
DB_PASS=$db_pass \
docker-compose up -d