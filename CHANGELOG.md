# Changelog

Todas as modificações relevantes ao  `expressive` serão documentadas nesse arquivo seguindo o especificado em [KEEP CHANGELOG](http://keepachangelog.com/).

## 1.2.0 - 2017-09-11

## Added
- Implementado conjunto whenReplicate no conjunto behavior de propriedades, definindo o comportamento do campo quando
em rotina de replicação. Ações válidas são 'clean', 'keep', 'last+1' e static, que respectivamente representam remoção 
do valor, manter valor, assumir valor do último registro somado a 1 e atribuir valor fixo ao campo. 

## 1.1.0  - 2017-08-29

## Changed
- Definido como padrão o retorno do método select como array de registros quando sucesso na consulta.

## 1.0.4  - 2017-07-23

## Fixed
- Substituido utilização expressão isset por empty no controle na classe controladora de transações.

## 1.0.3  - 2017-08-15

## Changed
- Atualizado método para tratamento de dependências no contexto Patch de modo a retornar resultado boleano para operação. 

## 1.0.2  - 2017-08-15

## Fixed
- Adicionado validação ao remover array de dependências na manipulação realizada pelo método hasMany implementada em RelationshipBuilder do contexto Patch.

## 1.0.1  - 2017-08-11

## Fixed
- Adicionado método para realizar rollback de transação ativa caso retorno false ao atualizar active record.

## 1.0.0  - 2017-08-08

## Added
- Incluido mecanismo de recursividade permitindo definir o número de filhos retornados pela operação de consulta.

## Changed
- Substituído instância concreta de schema por estática de modo a diminuir as operações de escrita/leitura no sistema de arquivos.
- Atualizado implementação de schema de modo a adicionar cache de valores estáticos como chaves incrementais e campos persistêntes.

## 0.3.0 - 2017-07-31

## Changed
- Modificado comportamento da entrada withDependencies utilizada em options do método select do active record, de modo que esse possa assumir, além 
  de valores lógicos, a forma de um array contendo a relação das propriedades esquematizadas como dependencias a serem consultadas.

## Added
- Adicionado entrada withProperties na propriedade options utilizada como parâmetro na função select do active record. A partir desta
  é possível especificar a relação de propriedades que serão retornadas pela consulta a persistência do respectivo model.

## 0.2.0 - 2017-07-20

## Added
- Atualizado versão dependências phpmagic e phpbreaker para versões 3.2.0 e 0.0.2 respectivamente.

## 0.1.0 - 2017-07-12

## Added
- Adicionado parâmetro no método active record search de modo a opcionalmente definir se irá retornar as respectivas dependências.

## 0.0.2 - 2017-07-08

## Added
- Adicionado dependências de illuminate Wrapper via construtor.
- Aprimorado tipos de retorno em assinaturas de métodos de interface

## 0.0.1 - 2017-07-07

### Added
- Adicionado código fonte e publicado versão 0.0.1
