<?php

declare(strict_types=1);

namespace PhpCollective\LaravelDjot\Tests\Rules;

use Illuminate\Support\Facades\Validator;
use PhpCollective\LaravelDjot\Rules\ValidDjot;
use PhpCollective\LaravelDjot\Tests\TestCase;

class ValidDjotTest extends TestCase
{
    public function testValidDjotPasses(): void
    {
        $validator = Validator::make(
            ['body' => 'Hello *world*!'],
            ['body' => [new ValidDjot()]],
        );

        $this->assertTrue($validator->passes());
    }

    public function testNullValuePasses(): void
    {
        $validator = Validator::make(
            ['body' => null],
            ['body' => [new ValidDjot()]],
        );

        $this->assertTrue($validator->passes());
    }

    public function testEmptyStringPasses(): void
    {
        $validator = Validator::make(
            ['body' => ''],
            ['body' => [new ValidDjot()]],
        );

        $this->assertTrue($validator->passes());
    }

    public function testComplexValidDjotPasses(): void
    {
        $djot = <<<'DJOT'
        # Heading

        A paragraph with *strong* and _emphasis_.

        - List item 1
        - List item 2

        > A blockquote
        DJOT;

        $validator = Validator::make(
            ['body' => $djot],
            ['body' => [new ValidDjot()]],
        );

        $this->assertTrue($validator->passes());
    }

    public function testNonStringFails(): void
    {
        $validator = Validator::make(
            ['body' => 123],
            ['body' => [new ValidDjot()]],
        );

        $this->assertFalse($validator->passes());
    }
}
