# README

## Expressive

Expressive é uma biblioteca para mapeamento objeto relacional  construída sobre o illuminate/database [illuminate/database](https://github.com/illuminate/database), com definições de objetos baseada em arquivos de schema.

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
    "repository": "tbproduto",
    "keys": [
      "empcodigo",
      "procodigo"
    ]
  },
  "properties": [
    {
      "alias": "iEmpCodigo",
      "property": "empcodigo",
      "type": "int"
    },
    {
      "alias": "iCodigo",
      "property": "procodigo",
      "type": "int",
      "behavior": {
        "autoIncrement": "true",
        "incrementalBehavior": "application"
      }
    },
    {
      "alias": "sDescricao",
      "property": "prodescricao",
      "type": "string"
    },
    {
      "alias": "iGicCodigo",
      "property": "giccodigo",
      "type": "int"
    },
    {
      "alias": "iGpcCodigo",
      "property": "gpccodigo",
      "type": "int"
    }
  ]
}
```

Uma Classe que utiliza a funcionalidade ORM fornecida pela biblioteca deve extender a class abstrata Expressive e ter em si declaradas as propriedades especificadas no schema json.

```
use Solis\Expressive\Classes\Illuminate\Expressive;
use Solis\Expressive\Magic\Concerns\HasMagic;

/**
 * Class Cst
 *
 * @package Sample\Postgres\Cst\Classes
 */
class Cst extends Expressive
{    
    use HasMagic;
    
    protected $empcodigo;
    protected $procodigo;
    protected $prodescricao;
    protected $giccodigo;
    protected $gpccodigo;
    protected $cstipicodigo;    

    /**
     * Produto constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->start(dirname(__FILE__) . '/Produto.json');
    }
}     
```

O método start é responsável por construir um objeto Schema a partir do caminho fornecido e atribuir a respectiva classe.

Uma classe extendo Expressive deve utilizar a trait HasMagic de modo ater acesso a funcionalidade de atribuição dinâmica por meio do método make.

```
$Produto = Produto::make(
    [
        "procodigo"     => 1,
        "empcodigo"     => 264,
        "iGicCodigo"    => 1,
        "iGpcCodigo"    => 1,
        "prodescricao"  => 'test [ ' . uniqid(rand()) . ' ]',        
    ]
);
``` 

O método make retorna uma instância de ExpressiveAbstract com acesso as operações de persistência disponibilizadas pela biblioteca.

### Operations

Relação de operações de persistência que podem ser utilizadas por uma instância de ExpressiveAbstract

#### Count

Retorna um valor inteiro representando a contagem de registros na persistência. 

```
(new Produto())->count(
    [
        [
            "column" => "empcodigo",
            "value"  => 264
        ]
    ]
)
```

#### Create

Cria um novo registro na persistência de acordo com os valores fornecidos para construção do objeto por meio do make.

```
$Produto = Produto::make(
    [
        "procodigo"     => 1,
        "empcodigo"     => 264,
        "iGicCodigo"    => 1,
        "iGpcCodigo"    => 1,
        "prodescricao"  => 'test [ ' . uniqid(rand()) . ' ]'
    ]
)->create();
```

#### Delete

Deleta um registro na persistência de acordo com as informações atribuidas ao active record.

```
Produto::make(
    [
        'empcodigo' => 264,
        'procodigo' => 1
    ]
)->search()->delete();
```

#### Last

Retorna o último registro na persistencia de acordo com as informações atribuidas ao active record.

```
Produto::make([
    'empcodigo' => 264
])->last()
```

### Replicate

Replica um registro existente na persistência de acordo com as informações atribuidas ao active record.

```
$instance = Produto::make([
    'iEmpCodigo' => 263,
])->last();

$instance->replicate();    
```

### Search

Retorna um registro existente na persistência de acordo com as informações atribuidas ao active record.

```
$instance = Produto::make([
    'procodigo' => 1,
    'empcodigo' => 264,
])->search();
```

### Select

Retorna uma relação de registros existentes na persistência de acordo a relação de argumentos e opções fornecidas.

```
$result = (new Produto())
    ->select();
```

### Update

Atualiza um registro existente na persistência de acordo com as informações atribuidas ao active record.

```
$instance = Produto::make(
    [
        'empcodigo' => 264,
        'procodigo' => 67,
    ]
)->search();

$instance->prodescricao = 'EMP 264 UPDATE TEST [ ' . Date('Y-m-d H:m:s') . ']';
$instance->giccodigo = 2;

$instance->update();
```

## Change log

Acompanhe o [CHANGELOG](CHANGELOG.md) para informações sobre atualizações recentes.

## Testing

(Em desenvolvimento)

```
$ composer test
```

## License

The MIT License (MIT). Verifique [LICENSE](LICENSE.MD) para mais informações.