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
 * Class SetWeightFunction
 * SetWeightFunction ::= "setweight" "(" StringPrimary "," StringPrimary")"
 *
 * @package Furniture\PostgresSearchBundle\DQL
 */
class SetWeightFunction extends FunctionNode
{
    public $char = null;

    public $tsVector = null;

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->tsVector = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->char = $parser->StringPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker)
    {
        return sprintf(
            'setweight(%s, %s)',
            $this->tsVector->dispatch($sqlWalker),
            $this->char->dispatch($sqlWalker)
        );
    }
}
