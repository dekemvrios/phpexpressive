# README

## Expressive

Expressive é uma bibliteca para mapeamento objeto relacionamento com definições de objetos baseada em arquivos de schema.

## Install

Esse pacote foi estruturado para ser instalado por meio do composer.

```
composer require solis/expressive
```
 
## Usage

Especifique a estrutura de um objeto de acordo com as especificações definidas por [phpschema](https://github.com/rafaelbeecker/phpschema).

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

Uma Classe que utiliza a funcionalidade ORM fornecida pela biblioteca deve extender a class abstrata Expressive e ter em si declaradas todas as propriedades especificadas no schema json.

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

Uma classe extendo Expressive deve utilizar a trait HasMagic, qual dispõe de uma funcionalidade de atribuição dinâmica por meio do método make.

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

O método make retorna uma instância com acessos as operações de persistência disponibilizadas pela biblioteca.


## Change log

Acompanhe o [CHANGELOG](CHANGELOG.md) para informações sobre atualizações recentes.

## Testing

(Em desenvolvimento)

```
$ composer test
```

## License

The MIT License (MIT). Verifique [LICENSE](LICENSE.MD) para mais informações.