# Changelog

Todas as modificações relevantes para phpexoressive serão documentadas neste arquivo

O formato é baseado [Keep a CHANGELOG](http://keepachangelog.com/) e esse projeto adere ao [Semantic Versioning 2.0.0](http://semver.org/).  

## 1.2.2 - 2017-09-18

## Fixed
- Corrigido erro qual resultava na atribuição de valor nulo aos campos definidos como obrigatórios no schema e não 
fornecidos ao model a ser atualizado no método update. A operação agora considera os valores originais da base de 
dados quando os campos obrigatórios não forem atribuidos ao model a ser atualizado.

## 1.2.1 - 2017-09-12

## Fixed
- Corrigida rotina de replicação de modo a não replicar dependência hasMany quando comportamento do schema prever ação 
clean.  

## 1.2.0 - 2017-09-11

## Added
- Implementado entrada whenReplicate no conjunto behavior de propriedades do schema, definindo o comportamento do campo 
quando em rotina de replicação. Ações válidas são 'clean', 'keep', 'last+1' e static, que, respectivamente, representam 
remoção do valor, manter valor, assumir valor do último registro somado a 1 e atribuir valor fixo ao campo. 

## 1.1.0  - 2017-08-29

## Changed
- Definido como padrão o retorno do método select como array de registros quando sucesso na consulta.

## 1.0.4  - 2017-07-23

## Fixed
- Substituido expressão isset por empty na classe Database controladora de transações, resolvendo situação de não 
efetivação de transações nas operações orm.

## 1.0.3  - 2017-08-15

## Fixed
- Atualizado método para tratamento de dependências no método patch de modo a retornar resultado boleano para operação. 

## 1.0.2  - 2017-08-15

## Fixed
- Corrigido rotina de remoção de dependências no método patch de modo a ignorar processo quando relação de dependências
estiver vazia.

## 1.0.1  - 2017-08-11

## Fixed
- Corrigido rotina de update de registro de modo a realizar rollback de transação ativa caso falha na operação.

## 1.0.0  - 2017-08-08

## Added
- Incluido mecanismo Digglet de recursividade permitindo definir o número de filhos retornados pela operação de consulta.

## Changed
- Substituído instância concreta de schema por estática de modo a diminuir as operações de escrita/leitura no sistema de 
arquivos.
- Atualizado implementação de schema de modo a adicionar cache de valores estáticos como chaves incrementais e campos 
persistentes.