# README

## Propósito
Expressive é uma biblioteca ORM implementada seguindo os conceitos aplicados com o padrão active record.

## Dependências
* "solis/phpbreaker": "0.0.1",
* "solis/phpmagic": "3.0.0",
* "illuminate/database": "^5.4",
* "illuminate/events": "^5.2"

## Como instalar?

Esse pacote foi estruturado para ser instalado por meio do composer como um repositório vcs

```
composer require solis/expressive 0.0.1
``` 

## Como utilizar?

Expressive utiliza o conceito de schema para determinar as propriedades utilizadas por um objeto. O schema deve ser uma string em formato json.
 
````
{
  "database": {
    "table": "tbcst",
    "primaryKeys": "cstcodigo"
  },
  "properties": [
    {
      "alias": "iCstCodigo",
      "property": "cstcodigo",
      "type": "int"
    }
  ]
}
```` 

Uma Classe que utiliza a funcionalidade ORM fornecida pela biblioteca deve extender a class abstrata Expressive e ter em si declaradas todas as propriedades especificadas no schema json.

```
use Solis\Expressive\Classes\Illuminate\Expressive;
use Solis\PhpMagic\Helpers\Magic;

/**
 * Class Cst
 *
 * @package Sample\Postgres\Cst\Classes
 */
class Cst extends Expressive
{    
    use Magic;

    protected $cstcodigo;    

    /**
     * Cst constructor.
     *
     */
    public function __construct()
    {
        parent::__construct(
            '/path/to/schema/Cst.json',
            'tbcst'
        );
    }
    
    /**
     * make
     *
     * @param array $dados
     *
     * @return static
     */
    public static function make($dados = [])
    {
        $instance = new static();
        if (!empty($dados)) {
            $instance->attach($dados);
        }

        return $instance;
    }
}
```

Uma classe extendo Expressive deve utilizar a trait Magic, qual dispõe de uma funcionalidade de atribuição dinâmica de propriedades por meio do método attach.

Dessa forma, é possível criar instâncias de uma determinada classe qual extende Expressive fornecendo um array associativo como argumento para um método factory.

```
$Cst = Cst::make([
    "iCsttipo"      => 2    
]);
```