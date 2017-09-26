# README

## Expressive

Expressive é uma biblioteca para mapeamento objeto relacional  construída sobre o [illuminate/database](https://github.com/illuminate/database), com definições de objetos baseada em arquivos de schema.

## Install

Esse pacote foi estruturado para ser instalado por meio do composer.

```
composer require solis/expressive
```
 
## Usage

Defina a estrutura de um objeto de acordo com as especificações do [phpschema](https://github.com/rafaelbeecker/phpschema).

```
{
  "database": {
    "repository": "pessoa",
    "keys": "ID"
  },
  "properties": [
    {
      "alias": "proID",
      "property": "ID",      
      "type": "int",
      "behavior": {        
        "required": "false",
        "autoIncrement": "true"
      }
    },
    {
      "alias": "proNome",
      "property": "nome",      
      "type": "string"
    },
    {
      "alias": "proInscricaoFederal",
      "property": "inscricaoFederal",      
      "type": "string"
    },
    {
      "alias": "proTipo",
      "property": "tipo",      
      "type": "int"
    },
    {
      "alias": "proSituacao",
      "property": "situacao",      
      "type": "int",
      "behavior": {
        "required":"false"
      }
    }
  ]
}
```

Uma Classe que utiliza a funcionalidade ORM fornecida pela biblioteca deve extender a class abstrata Expressive e ter em si declaradas as propriedades especificadas no schema json.

```
use Solis\Expressive\Classes\Illuminate\Expressive;
use Solis\Expressive\Magic\Concerns\HasMagic;

class Pessoa extends Expressive
{
    use HasMagic;

    protected $ID;
    protected $nome;
    protected $inscricaoFederal;
    protected $tipo;
    protected $situacao;
    
    /**
     * Pessoa constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->start(dirname(__FILE__) . '/Pessoa.json');
    }
}   
```

O método start é responsável por construir um objeto Schema a partir do caminho fornecido e atribuir a respectiva classe.

Uma classe extendo Expressive deve utilizar a trait HasMagic de modo ater acesso a funcionalidade de atribuição dinâmica por meio do método make.

```
$Pessoa = Pessoa::make([
      "proNome"             => 'Fulano - ' . uniqid(rand()),
      "proInscricaoFederal" => '' . rand(11111111111111, 99999999999999) . '',
      "proTipo"             => 1      
]);
``` 

O método make retorna uma instância de ExpressiveAbstract com acesso as operações de persistência disponibilizadas pela biblioteca.

### Operations

Relação de operações de persistência que podem ser utilizadas por uma instância de ExpressiveAbstract

#### Count

Retorna um valor inteiro representando a contagem de registros na persistência. 

```
$count = (new Pessoa()) ->count()

//$count = Pessoa::make([])->last($arguments = []);
```

#### Create

Cria um novo registro na persistência de acordo com os valores fornecidos para construção do objeto por meio do make.

```
Pessoa::make([
      "proNome"             => 'Fulano - ' . uniqid(rand()),
      "proInscricaoFederal" => '' . rand(11111111111111, 99999999999999) . '',
      "proTipo"             => 1      
])->create();
```

#### Delete

Deleta um registro na persistência de acordo com as informações atribuidas ao active record.

```
Pessoa::make([
        'proID' => 1
])->search()->delete();

```

#### Last

Retorna o último registro na persistencia de acordo com as informações atribuidas ao active record.

```
(new Pessoa())->last()

//Pessoa::make([])->last();
```

### Replicate

Replica um registro existente na persistência de acordo com as informações atribuidas ao active record.

```
$instance = Pessoa::make([
    'proID' => 1,
])->last();

$instance->replicate();    
```

### Search

Retorna um registro existente na persistência de acordo com as informações atribuidas ao active record.

```
$instance = Pessoa::make([
    'proID' => 1
])->search();
```

### Select

Retorna uma relação de registros existentes na persistência de acordo a relação de argumentos e opções fornecidas.

```
$result = (new Pessoa())->select();

//$result = Pessoa::make([])->select($arguments = [], $options = []);
```

### Update

Atualiza um registro existente na persistência de acordo com as informações atribuidas ao active record.

```
$instance = Pessoa::make(
    [
        'proID' => 1,        
    ]
)->search();

$instance->proNome = 'UPDATE TEST [ ' . Date('Y-m-d H:m:s') . ']';

$instance->update();
```

## Change log

Acompanhe o [CHANGELOG](CHANGELOG.md) para informações sobre atualizações recentes.

## Testing

```
$ composer test
```

## License

The MIT License (MIT). Verifique [LICENSE](LICENSE.MD) para mais informações.