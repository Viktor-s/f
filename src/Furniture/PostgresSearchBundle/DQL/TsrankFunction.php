<?php
/**
 * Created by Shtompel Konstantin.
 * User: synthetic
 * Date: 3/23/2016
 * Time: 11:57 AM
 */

namespace Furniture\PostgresSearchBundle\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * Class TsrankFunction
 * TsrankFunction ::= "ts_rank" "(" StringPrimary "," StringPrimary ")"
 *
 * @package Furniture\PostgresSearchBundle\DQL
 */
class TsrankFunction extends FunctionNode
{
    public $fieldName = null;
    public $queryString = null;

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->fieldName = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->queryString = $parser->StringPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker)
    {
        return sprintf(
            'ts_rank(%s, plainto_tsquery(%s))',
            $this->fieldName->dispatch($sqlWalker),
            $this->queryString->dispatch($sqlWalker)
        );
    }
}