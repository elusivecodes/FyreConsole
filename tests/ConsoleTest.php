<?php
declare(strict_types=1);

namespace Tests;

use Fyre\Console\Console;
use PHPUnit\Framework\TestCase;

use function exec;
use function file_get_contents;
use function file_put_contents;
use function fopen;
use function unlink;

use const LOCK_EX;
use const PHP_EOL;

final class ConsoleTest extends TestCase
{
    protected static $in = __DIR__.'/input';

    protected static $out = __DIR__.'/output';

    protected Console $console;

    public function testChoice(): void
    {
        file_put_contents(self::$in, 'a'."\r\n", LOCK_EX);

        $this->assertSame(
            'a',
            $this->console->choice('Select one', ['a', 'b', 'c'])
        );
        $this->assertSame(
            "\033[0;33mSelect one\033[0m".PHP_EOL.
            " (\033[2;36ma\033[0m/\033[2;36mb\033[0m/\033[2;36mc\033[0m)".PHP_EOL,
            file_get_contents(self::$out)
        );
    }

    public function testChoiceAssoc(): void
    {
        file_put_contents(self::$in, 'b'."\r\n", LOCK_EX);

        $this->assertSame(
            'b',
            $this->console->choice('Select one', ['a' => 'Test 1', 'b' => 'Test 2', 'c' => 'Test 3'], 'a')
        );
        $this->assertSame(
            "\033[0;33mSelect one\033[0m".PHP_EOL.
            "\033[0;36m  [a]  \033[0m\033[2;37mTest 1\033[0m".PHP_EOL.
            "\033[0;36m  [b]  \033[0m\033[2;37mTest 2\033[0m".PHP_EOL.
            "\033[0;36m  [c]  \033[0m\033[2;37mTest 3\033[0m".PHP_EOL.
            "\033[0;33mChoice\033[0m (\033[1;36ma\033[0m/\033[2;36mb\033[0m/\033[2;36mc\033[0m)".PHP_EOL,
            file_get_contents(self::$out)
        );
    }

    public function testChoiceDefault(): void
    {
        file_put_contents(self::$in, 'x'."\r\n", LOCK_EX);

        $this->assertSame(
            'a',
            $this->console->choice('Select one', ['a', 'b', 'c'], 'a')
        );
        $this->assertSame(
            "\033[0;33mSelect one\033[0m".PHP_EOL.
            " (\033[1;36ma\033[0m/\033[2;36mb\033[0m/\033[2;36mc\033[0m)".PHP_EOL,
            file_get_contents(self::$out)
        );
    }

    public function testComment(): void
    {
        $this->console->comment('Test');

        $this->assertSame(
            "\033[2;37mTest\033[0m".PHP_EOL,
            file_get_contents(self::$out)
        );
    }

    public function testConfirm(): void
    {
        file_put_contents(self::$in, 'n'."\r\n", LOCK_EX);

        $this->assertFalse($this->console->confirm('OK?'));
        $this->assertSame(
            "\033[0;33mOK?\033[0m".PHP_EOL.
            " (\033[1;36my\033[0m/\033[2;36mn\033[0m)".PHP_EOL,
            file_get_contents(self::$out)
        );
    }

    public function testConfirmDefault(): void
    {
        file_put_contents(self::$in, 'x'."\r\n", LOCK_EX);

        $this->assertTrue($this->console->confirm('OK?'));
        $this->assertSame(
            "\033[0;33mOK?\033[0m".PHP_EOL.
            " (\033[1;36my\033[0m/\033[2;36mn\033[0m)".PHP_EOL,
            file_get_contents(self::$out)
        );
    }

    public function testError(): void
    {
        $this->console->error('Test');

        $this->assertSame(
            "\033[0;31mTest\033[0m".PHP_EOL,
            file_get_contents(self::$out)
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

    public function testInfo(): void
    {
        $this->console->info('Test');

        $this->assertSame(
            "\033[0;34mTest\033[0m".PHP_EOL,
            file_get_contents(self::$out)
        );
    }

    public function testInput(): void
    {
        file_put_contents(self::$in, 'This is some test input'."\r\n", LOCK_EX);

        $this->assertSame(
            'This is some test input',
            $this->console->input()
        );
    }

    public function testProgress(): void
    {
        $this->console->progress(5);

        $this->assertSame(
            "[\033[0;32m#####.....\033[0m] 50%".PHP_EOL,
            file_get_contents(self::$out)
        );

        $this->console->progress();
    }

    public function testProgressClear(): void
    {
        $this->console->progress(5);
        $this->console->progress();

        $this->assertSame(
            "[\033[0;32m#####.....\033[0m] 50%".PHP_EOL."\033[1A\033[K\007",
            file_get_contents(self::$out)
        );
    }

    public function testProgressTotalSteps(): void
    {
        $this->console->progress(25, 100);

        $this->assertSame(
            "[\033[0;32m###.......\033[0m] 25%".PHP_EOL,
            file_get_contents(self::$out)
        );

        $this->console->progress();
    }

    public function testPrompt(): void
    {
        file_put_contents(self::$in, 'This is some test input'."\r\n", LOCK_EX);

        $this->assertSame(
            'This is some test input',
            $this->console->prompt('This is a prompt')
        );
        $this->assertSame(
            "\033[0;33mThis is a prompt\033[0m".PHP_EOL,
            file_get_contents(self::$out)
        );
    }

    public function testStyle(): void
    {
        $this->assertSame(
            'Test',
            Console::style('Test')
        );
    }

    public function testStyleBackground(): void
    {
        $this->assertSame(
            "\033[0;37;44mTest\033[0m",
            Console::style('Test', [
                'bg' => Console::BLUE,
            ])
        );
    }

    public function testStyleBold(): void
    {
        $this->assertSame(
            "\033[1;37mTest\033[0m",
            Console::style('Test', [
                'style' => Console::BOLD,
            ])
        );
    }

    public function testStyleColor(): void
    {
        $this->assertSame(
            "\033[0;34mTest\033[0m",
            Console::style('Test', [
                'color' => Console::BLUE,
            ])
        );
    }

    public function testStyleDim(): void
    {
        $this->assertSame(
            "\033[2;37mTest\033[0m",
            Console::style('Test', [
                'style' => Console::DIM,
            ])
        );
    }

    public function testStyleFlash(): void
    {
        $this->assertSame(
            "\033[5;37mTest\033[0m",
            Console::style('Test', [
                'style' => Console::FLASH,
            ])
        );
    }

    public function testStyleItalic(): void
    {
        $this->assertSame(
            "\033[3;37mTest\033[0m",
            Console::style('Test', [
                'style' => Console::ITALIC,
            ])
        );
    }

    public function testStyleUnderline(): void
    {
        $this->assertSame(
            "\033[4;37mTest\033[0m",
            Console::style('Test', [
                'style' => Console::UNDERLINE,
            ])
        );
    }

    public function testSuccess(): void
    {
        $this->console->success('Test');

        $this->assertSame(
            "\033[0;32mTest\033[0m".PHP_EOL,
            file_get_contents(self::$out)
        );
    }

    public function testTable(): void
    {
        $this->console->table([
            ['1', '2', '3'],
            ['Test', 'Value', '0'],
        ]);

        $this->assertSame(
            '+------+-------+---+'.PHP_EOL.
            '| 1    | 2     | 3 |'.PHP_EOL.
            '| Test | Value | 0 |'.PHP_EOL.
            '+------+-------+---+'.PHP_EOL,
            file_get_contents(self::$out)
        );
    }

    public function testTableColor(): void
    {
        $this->console->table([
            ['1', '2', '3'],
            [Console::style('Test', ['color' => Console::BLUE]), 'Value', '0'],
        ]);

        $this->assertSame(
            '+------+-------+---+'.PHP_EOL.
            '| 1    | 2     | 3 |'.PHP_EOL.
            "| \033[0;34mTest\033[0m | Value | 0 |".PHP_EOL.
            '+------+-------+---+'.PHP_EOL,
            file_get_contents(self::$out)
        );
    }

    public function testTableHeader(): void
    {
        $this->console->table([
            ['1', '2', '3'],
            ['Test', 'Value', '0'],
        ], [
            'A',
            'B',
            'C',
        ]);

        $this->assertSame(
            '+------+-------+---+'.PHP_EOL.
            '| A    | B     | C |'.PHP_EOL.
            '+------+-------+---+'.PHP_EOL.
            '| 1    | 2     | 3 |'.PHP_EOL.
            '| Test | Value | 0 |'.PHP_EOL.
            '+------+-------+---+'.PHP_EOL,
            file_get_contents(self::$out)
        );
    }

    public function testWarning(): void
    {
        $this->console->warning('Test');

        $this->assertSame(
            "\033[0;33mTest\033[0m".PHP_EOL,
            file_get_contents(self::$out)
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

    public function testWrite(): void
    {
        $this->console->write('Test');

        $this->assertSame(
            'Test'.PHP_EOL,
            file_get_contents(self::$out)
        );
    }

    protected function setUp(): void
    {
        file_put_contents(self::$in, '', LOCK_EX);
        file_put_contents(self::$out, '', LOCK_EX);

        $input = fopen(self::$in, 'r');
        $output = fopen(self::$out, 'w');

        $this->console = new Console($input, $output, $output);
    }

    protected function tearDown(): void
    {
        @unlink(self::$in);
        @unlink(self::$out);
    }
}
