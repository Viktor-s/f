<?php
/**
 * Created by Shtompel Konstantin.
 * User: synthetic
 * Date: 3/23/2016
 * Time: 11:56 AM
 */

namespace Furniture\PostgresSearchBundle\DQL;

use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;

/**
 * Class TsheadlineFunction
 * TsheadlineFunction ::= "ts_headline" "(" StringPrimary "," StringPrimary ")"
 *
 * @package Furniture\PostgresSearchBundle\DQL
 */
class TsheadlineFunction extends FunctionNode
{
    /**
     * @var null
     */
    public $fieldName = null;
    public $queryString = null;

    /**
     * {@inheritdoc}
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->fieldName = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->queryString = $parser->StringPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    /**
     * {@inheritdoc}
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        return sprintf(
            'ts_headline(%s, plainto_tsquery(%s), "%s")',
            $this->fieldName->dispatch($sqlWalker),
            $this->queryString->dispatch($sqlWalker),
            'StartSel = <mark>, StopSel = </mark>, HighlightAll=FALSE'
        );
    }
}
