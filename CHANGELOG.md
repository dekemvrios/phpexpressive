# Changelog

Todas as modificações relevantes para phpexoressive serão documentadas neste arquivo

O formato é baseado [Keep a CHANGELOG](http://keepachangelog.com/) e esse projeto adere ao [Semantic Versioning 2.0.0](http://semver.org/).  

## 1.6.3 - 2018-03-23

## Added
- Adicionado suporte a tipo schema bool e boolean para operadores relacionados ao construir query de consulta der registro.

## Fixed
- Removidos campos json de operadores não suportados ao construir query de consulta de registro.

## 1.6.2 - 2018-03-22

## Added
- Adicionado parâmetro times ao método de replicação, possibilitando retorno de array contendo registros replicados a 
partir de model consultado.

## Changed
- Atribuido mensagem explicativa de erro a item mensagem de Exception lançada por operações de persistência, sobrescrevendo
comportamento de classe abstrata TExceptionAbstract.

## 1.6.1 - 2018-02-27

## Changed
- Aprimorado construção de query de métodos select e count de modo a desconsiderar coluna especificada no conjunto de 
argumentos quando essa não estiver presente no schema do registro a ser consultado, ou, quando o operador solicitado é 
incompatível com o tipo do respectivo campo.

## 1.6.0 - 2018-01-18

## Added
- Implementado método de delação virtual de registro. Se o model possuir uma propredade com nome de 'active', é possível 
atribuir false a seu valor por meio do método 'disable' no contexto do model.

## 1.5.0 - 2017-11-07

## Added
- Implementado campo commonFields na relação schema de composição, permitindo compartilhar valores entre registro e
suas respectivas dependências.

## 1.4.0 - 2017-10-19

## Added
- Adicionado suporte a nested queries no contexto de utilização do método select.

## 1.3.2 - 2017-10-18

## Changed
- Refatorada classes envolvidas no contexto de construção de query nas operações de select e serach.

## 1.3.1 - 2017-09-27

## Changed
- Adicionado no README badges do Codacy e aprimorado testes.

## 1.3.0 - 2017-09-26

## Added
- Implementado entrada whenPatch no conjunto behavior de propriedades do schema, definindo o comportamento do campo 
quando em rotina de atualização de registro. Ações válidas são 'keep' e 'update', que, respectivamente, representam 
manter valor original ou assumir valor fornecido no campo do registro a ser atualizado.
- Incluido teste de integração das operações disponibilizadas pelo Expressive.

## 1.2.4 - 2017-09-22

## Fixed
- Corrigido incosistência na execução do método update qual ocasionava erro ao tentar atualizar registro dependência
has One quando não estava atribuido valor no respectivo campo na base de dados.

## 1.2.3 - 2017-09-18 

## Changed
- Refatorado mecanismo Digglet de modo a favorecer legibilidade de código e adicionado possibilidade de definição de 
nivel de dependências como 0 no controle de recursividade para os métodos search e select.

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