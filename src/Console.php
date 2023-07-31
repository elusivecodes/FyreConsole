<?php
declare(strict_types=1);

namespace Fyre\Console;

use NumberFormatter;
use RuntimeException;

use const PHP_EOL;
use const PHP_INT_MAX;
use const PREG_SPLIT_DELIM_CAPTURE;
use const STDERR;
use const STDOUT;

use function array_filter;
use function array_map;
use function array_unshift;
use function count;
use function exec;
use function fwrite;
use function implode;
use function max;
use function min;
use function preg_match;
use function preg_split;
use function readline;
use function round;
use function str_repeat;
use function stream_isatty;
use function strtr;
use function wordwrap;

/**
 * Console
 */
abstract class Console
{

    public const BACKGROUND_COLORS = [
        'black' => '40',
        'red' => '41',
        'green' => '42',
        'yellow' => '43',
        'blue' => '44',
        'purple' => '45',
        'cyan' => '46',
        'light_gray' => '47'
    ];

    public const FOREGROUND_COLORS = [
        'black' => '0;30',
        'red' => '0;31',
        'green' => '0;32',
        'yellow' => '0;33',
        'blue' => '0;34',
        'purple' => '0;35',
        'cyan' => '0;36',
        'light_gray' => '0;37',
        'dark_gray' => '1;30',
        'light_red' => '1;31',
        'light_green' => '1;32',
        'light_yellow' => '1;33',
        'light_blue' => '1;34',
        'light_purple' => '1;35',
        'light_cyan' => '1;36',
        'white' => '1;37'
    ];

    protected const TOTAL_STEPS = 10;

    protected static int|null $lastStep = null;

    protected static NumberFormatter $percentFormatter;

    /**
     * Color a string for terminal output, preserving existing colors.
     * @param string $text The text.
     * @param array $options The color options.
     * @return string The colored text.
     */
    public static function color(string $text, array $options = []): string
    {
        if (!$text || $options === []) {
            return $text;
        }

        $pattern = '/(\\033\\[.+?\\033\\[0m)/u';

        $strings = preg_split($pattern, $text, -1, PREG_SPLIT_DELIM_CAPTURE);

        $strings = array_map(
            fn(string $string): string =>
                preg_match($pattern, $string) ?
                    $string :
                    static::colorize($string, $options),
            array_filter($strings)
        );

        return implode('', $strings);
    }

    /**
     * Output text to STDERR.
     * @param string $text The text.
     * @param array $options The color options.
     */
    public static function error(string $text, array $options = []): void
    {
        $options['foreground'] ??= 'light_red';

        if (stream_isatty(STDERR)) {
            $text = static::color($text, $options);
        }

        fwrite(STDERR, $text.PHP_EOL);
    }

    /**
     * Get the terminal height (in characters).
     * @return int The terminal height.
     */
    public static function getHeight(): int
    {
        return (int) exec('tput lines');
    }

    /**
     * Get the terminal width (in characters).
     * @return int The terminal width.
     */
    public static function getWidth(): int
    {
        return (int) exec('tput cols');
    }

    /**
     * Read a line of input.
     * @param string $prefix The prefix.
     * @return string The input text.
     */
    public static function input(string $prefix = ''): string
    {
        return readline($prefix);
    }

    /**
     * Output a progress indicator.
     * @param int|null $step The step.
     * @param int $totalSteps The total steps.
     */
    public static function progress(int|null $step = null, int $totalSteps = 10): void
    {
        if ($step === null) {
            static::$lastStep = $step;
    
            fwrite(STDOUT, "\033[1A\033[K");
            fwrite(STDOUT, "\007");
            return;
        }

        if (static::$lastStep && static::$lastStep <= $step) {
            fwrite(STDOUT, "\r\033[1A\r\033[K\r");
        }

        static::$lastStep = $step;

        $step = max($step, 1);
        $totalSteps = max($totalSteps, 1);

        $percent = $step / $totalSteps;
        $step = (int) round($percent * static::TOTAL_STEPS);

        $progressString = str_repeat('#', $step).
            str_repeat('.', static::TOTAL_STEPS - $step);

        $percentString = static::percentFormatter()->format($percent);

        fwrite(STDOUT, '['.static::colorize($progressString, ['foreground' => 'light_green']).'] '.$percentString.PHP_EOL);
    }

    /**
     * Output a table.
     * @param array $data The table rows.
     * @param array $header The table header columns.
     */
    public static function table(array $data, array $header = []): void
    {
        if ($header !== []) {
            array_unshift($data, $header);
        }

        $maxLengths = [];

        foreach ($data AS $row) {
            foreach ($row AS $i => $value) {
                $maxLengths[$i] ??= 0;
                $maxLengths[$i] = max($maxLengths[$i], static::strlen((string) $value));
            }
        }

        $border = '+';
        foreach ($maxLengths AS $length) {
            $border .= str_repeat('-', $length + 2).'+';
        }
        $border .= PHP_EOL;

        foreach ($data AS $i => $row) {
            foreach ($row AS $j => $value) {
                $diff = $maxLengths[$j] - static::strlen((string) $value);
                $data[$i][$j] .= str_repeat(' ', $diff);
            }
        }

        $rowCount = count($data);

        $table = '';

        foreach ($data AS $i => $row) {
            if ($i === 0) {
                $table .= $border;
            }

            $table .= '| '.implode(' | ', $row).' |'.PHP_EOL;

            if (($i === 0 && $header !== []) || $i === $rowCount - 1) {
                $table .= $border;
            }
        }

        fwrite(STDOUT, $table);
    }

    /**
     * Output text to STDOUT.
     * @param string $text The text.
     * @param array $options The color options.
     */
    public static function write(string $text, array $options = []): void
    {
        if (stream_isatty(STDOUT)) {
            $text = static::color($text, $options);
        }

        fwrite(STDOUT, $text.PHP_EOL);
    }

    /**
     * Wrap text for terminal output.
     * @param string $text The text.
     * @param int|null $maxWidth The maximum width.
     */
    public static function wrap(string $text, int|null $maxWidth = null): string
    {
        $maxWidth = min($maxWidth ?? PHP_INT_MAX, static::getWidth());

        return wordwrap($text, $maxWidth, PHP_EOL);
    }

    /**
     * Color a string for terminal output.
     * @param string $text The text.
     * @param array $options The color options.
     * @return string The colored text.
     */
    protected static function colorize(string $text, array $options): string
    {
        $foreground = $options['foreground'] ?? null;
        $background = $options['background'] ?? null;
        $underline = $options['underline'] ?? false;

        if (!$foreground && !$background && !$underline) {
            return $text;
        }

        if ($foreground && !array_key_exists($foreground, static::FOREGROUND_COLORS)) {
            throw new RuntimeException('Invalid color: '.$foreground);
        }

        if ($background && !array_key_exists($background, static::BACKGROUND_COLORS)) {
            throw new RuntimeException('Invalid background color: '.$background);
        }

        $result = '';

        if ($foreground) {
            $result .= "\033[".static::FOREGROUND_COLORS[$foreground].'m';
        }

        if ($background) {
            $result .= "\033[".static::BACKGROUND_COLORS[$background].'m';
        }

        if ($underline) {
            $result .= "\033[4m";
        }

        $result .= $text;
        $result .= "\033[0m";

        return $result;
    }

    /**
     * Create a percent formatter.
     * @return NumberFormatter The percent formatter.
     */
    protected static function percentFormatter(): NumberFormatter
    {
        return static::$percentFormatter ??= new NumberFormatter('en_US', NumberFormatter::PERCENT);
    }

    /**
     * Get the real length of a string.
     * @param string $string The string.
     * @return int The length.
     */
    protected static function strlen(string $string): int
    {
        $replacements = [
            "\033[4m" => '',
            "\033[0m" => ''
        ];

        foreach (static::FOREGROUND_COLORS AS $color) {
            $replacements["\033[".$color.'m'] = '';
        }

        foreach (static::BACKGROUND_COLORS AS $color) {
            $replacements["\033[".$color.'m'] = '';
        }

        $string = strtr($string, $replacements);

        return mb_strwidth($string);
    }

}
