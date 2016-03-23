<?php
/**
 * Created by Shtompel Konstantin.
 * User: synthetic
 * Date: 3/23/2016
 * Time: 11:56 AM
 */

namespace Furniture\PostgresSearchBundle\DQL;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;

/**
 * Class TsqueryFunction
 * TsqueryFunction ::= "to_tsquery" "(" StringPrimary "," StringPrimary "," StringPrimary ")"
 *
 * @package Furniture\PostgresSearchBundle\DQL
 */
class TsqueryFunction extends FunctionNode
{
    public $fieldName = null;

    public $queryString = null;

    public $regconfig = null;

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->fieldName = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->queryString = $parser->StringPrimary();

        if ($parser->getLexer()->lookahead['type'] == Lexer::T_COMMA) {
            $parser->match(Lexer::T_COMMA);
            $this->regconfig = $parser->StringPrimary();
        }

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker)
    {
        return sprintf(
            '%s @@ to_tsquery(%s%s)',
            $this->fieldName->dispatch($sqlWalker),
            (!is_null($this->regconfig) ? $this->regconfig->dispatch($sqlWalker).', ' : ''),
            $this->queryString->dispatch($sqlWalker)
        );
    }
}
