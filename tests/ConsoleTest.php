<?php
declare(strict_types=1);

namespace Tests;

use
    Fyre\Console\Console,
    PHPUnit\Framework\TestCase,
    RuntimeException,
    Tests\Mock\StreamFilter;

use const
    PHP_EOL;

use function
    exec,
    stream_filter_append,
    stream_filter_remove;

final class ConsoleTest extends TestCase
{

    protected $filter;

    public function testColor(): void
    {
        $this->assertSame(
            'Test',
            Console::color('Test')
        );
    }

    public function testColorForeground(): void
    {
        $this->assertSame(
            "\033[0;34mTest\033[0m",
            Console::color('Test', [
                'foreground' => 'blue'
            ])
        );
    }

    public function testColorBackground(): void
    {
        $this->assertSame(
            "\033[44mTest\033[0m",
            Console::color('Test', [
                'background' => 'blue'
            ])
        );
    }

    public function testColorUnderline(): void
    {
        $this->assertSame(
            "\033[4mTest\033[0m",
            Console::color('Test', [
                'underline' => true
            ])
        );
    }

    public function testColorInvalid(): void
    {
        $this->expectException(RuntimeException::class);

        Console::color('Test', [
            'foreground' => 'invalid'
        ]);
    }

    public function testColorBackgroundInvalid(): void
    {
        $this->expectException(RuntimeException::class);

        Console::color('Test', [
            'background' => 'invalid'
        ]);
    }

    public function testColorMerges(): void
    {
        $this->assertSame(
            "\033[0;34mTest\033[0m\033[0;31mTest\033[0m\033[0;34mTest\033[0m",
            Console::color("\033[0;34mTest\033[0mTest\033[0;34mTest\033[0m", [
                'foreground' => 'red'
            ])
        );
    }

    public function testGetHeight(): void
    {
        $this->assertSame(
            (int) exec('tput lines'),
            Console::getHeight()
        );
    }

    public function testGetWidth(): void
    {
        $this->assertSame(
            (int) exec('tput cols'),
            Console::getWidth()
        );
    }

    public function testProgress(): void
    {
        Console::progress(5);

        $this->assertSame(
            "[\033[1;32m#####.....\033[0m] 50%".PHP_EOL,
            StreamFilter::getBuffer()
        );

        Console::progress();
    }

    public function testProgressTotalSteps(): void
    {
        Console::progress(25, 100);

        $this->assertSame(
            "[\033[1;32m###.......\033[0m] 25%".PHP_EOL,
            StreamFilter::getBuffer()
        );

        Console::progress();
    }

    public function testProgressClear(): void
    {
        Console::progress(5);
        Console::progress();

        $this->assertSame(
            "[\033[1;32m#####.....\033[0m] 50%".PHP_EOL."\033[1A\033[K\007",
            StreamFilter::getBuffer()
        );
    }

    public function testTable(): void
    {
        Console::table([
            ['1', '2', '3'],
            ['Test', 'Value', '0']
        ]);

        $this->assertSame(
            '+------+-------+---+'.PHP_EOL.
            '| 1    | 2     | 3 |'.PHP_EOL.
            '| Test | Value | 0 |'.PHP_EOL.
            '+------+-------+---+'.PHP_EOL,
            StreamFilter::getBuffer()
        );
    }

    public function testTableHeader(): void
    {
        Console::table([
            ['1', '2', '3'],
            ['Test', 'Value', '0']
        ], [
            'A',
            'B',
            'C'
        ]);

        $this->assertSame(
            '+------+-------+---+'.PHP_EOL.
            '| A    | B     | C |'.PHP_EOL.
            '+------+-------+---+'.PHP_EOL.
            '| 1    | 2     | 3 |'.PHP_EOL.
            '| Test | Value | 0 |'.PHP_EOL.
            '+------+-------+---+'.PHP_EOL,
            StreamFilter::getBuffer()
        );
    }

    public function testTableColor(): void
    {
        Console::table([
            ['1', '2', '3'],
            [Console::color('Test', ['foreground' => 'blue']), 'Value', '0']
        ]);

        $this->assertSame(
            '+------+-------+---+'.PHP_EOL.
            '| 1    | 2     | 3 |'.PHP_EOL.
            "| \033[0;34mTest\033[0m | Value | 0 |".PHP_EOL.
            '+------+-------+---+'.PHP_EOL,
            StreamFilter::getBuffer()
        );
    }

    public function testWrite(): void
    {
        Console::write('Test');

        $this->assertSame(
            'Test'.PHP_EOL,
            StreamFilter::getBuffer()
        );
    }

    public function testWrap(): void
    {
        $this->assertSame(
            'This'.PHP_EOL.
            'is a'.PHP_EOL.
            'test'.PHP_EOL.
            'string',
            Console::wrap('This is a test string', 5)
        );
    }

    protected function setUp(): void
    {
        $this->filter = stream_filter_append(STDOUT, 'StreamFilter');
    }

    protected function tearDown(): void
    {
        StreamFilter::clear();

        stream_filter_remove($this->filter);
    }

    public static function setUpBeforeClass(): void
    {
        StreamFilter::register();
    }

}
