<?php

namespace Opsway\Tests\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\ParenthesisExpression;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Opsway\Doctrine\ORM\Query\AST\Functions\Unnest;
use PHPUnit\Framework\TestCase;

class UnnestTest extends TestCase
{
    private $unnest;

    public function setUp()
    {
        $this->unnest = new Unnest('test');
    }

    public function testFunction()
    {
        $parser = $this->prophesize(Parser::class);
        $expr = $this->prophesize(ParenthesisExpression::class);

        $parser->match()->shouldBeCalled()->withArguments([Lexer::T_IDENTIFIER]);
        $parser->match()->shouldBeCalled()->withArguments([Lexer::T_OPEN_PARENTHESIS]);
        $parser->ArithmeticPrimary()->shouldBeCalled()->willReturn($expr->reveal());
        $parser->match()->shouldBeCalled()->withArguments([Lexer::T_CLOSE_PARENTHESIS]);
        $sqlWalker = $this->prophesize(SqlWalker::class);

        $this->unnest->parse($parser->reveal());
        $expr->dispatch()->shouldBeCalled()->withArguments([$sqlWalker->reveal()])->willReturn('test');

        $this->assertEquals('UNNEST(test)', $this->unnest->getSql($sqlWalker->reveal()));
    }
}
