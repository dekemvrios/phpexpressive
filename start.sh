#!/bin/sh

# |----------------------------------------------------------------------------
# | Script de inicialização do container docker para executar a aplicação
# |----------------------------------------------------------------------------
# |
# | Valida o tipo de script a ser executado de acordo com o argumento fornecido.
# | Caso nenhum argumento for fornecido o container será inicializado. Para que
# | seja desligado é necessário que a literal 'down' seja fornecida como argumento.
# |
arg="$1"

type_script="up -d"
case "$arg" in
"down") type_script="down";;
*)
esac

# |----------------------------------------------------------------------------
# | Definir valor de enderço de IP da máquina local
# |----------------------------------------------------------------------------
# |
# | Captura valor de enderço de IP da máquina local de modo a ser utilizada na
# | conexão com o debugger da aplicação.
# |
ip_addres=$(eval hostname -I | awk '{print $1}')

# |----------------------------------------------------------------------------
# | Definir valor do diretório no qual o manager está localizado
# |----------------------------------------------------------------------------
# |
# | Captura o caminho para o diretório atual e o atribui como diretório onde o
# | manager está localizado. Este sera utilizado para definição dos subdiretórios
# | da aĺicação.
# |
current_directory=$(eval pwd)

# |----------------------------------------------------------------------------
# | Definir valor do diretório root da aplicação
# |----------------------------------------------------------------------------
# |
# | Utiliza o valor de current_directory de modo a definir o caminho do root da
# | aplicação, que será mapeado no container docker.
# |
nginx_root=$current_directory

# |----------------------------------------------------------------------------
# | Definir valor do caminho para o arquivo docker
# |----------------------------------------------------------------------------
# |
# | Com base no valor de current_directory, define o caminho para o arquivo docker
# | que será utilizado para iniciar a aplicação.
# |
docker_compose_file=$current_directory/sample/environment/docker/docker-compose.yml

docker_file=$current_directory/sample/environment/docker/Dockerfile

# |----------------------------------------------------------------------------
# | Definir valor do caminho para o arquivo de configuração apache
# |----------------------------------------------------------------------------
# |
# | Com base no valor de current_directory, define o caminho para o arquivo nginx
# | utilizado para configuração do ambiente servidor.
# |
nginx_conf=$current_directory/sample/environment/nginx/site.conf

# |----------------------------------------------------------------------------
# | Definir valor do caminho para o diretório que contém as variáveis de ambiente
# |----------------------------------------------------------------------------
# |
# | Com base no valor de current_directory, define o caminho para o diretório que
# | contém as variáveis de ambiente que serão utilizadas pela aplicação.
# |
variables_path=$current_directory/sample/environment/variables

# |----------------------------------------------------------------------------
# | Inicializa a aplicação
# |----------------------------------------------------------------------------
# |
# | Executa o comando docker-compose de modo a inicializar a aplicação com base
# | nos valores definidos anteriormente.
# |

IP=$ip_addres \
NGINX_ROOT=$nginx_root \
NGINX_CONF=$nginx_conf \
VARIABLES_PATH=$variables_path \
INSTALL_XDEBUG=true \
DEBUG=1 \
DOCKER_FILE=$docker_file \
docker-compose -f $docker_compose_file up --build