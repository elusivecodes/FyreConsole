<?php
declare(strict_types=1);

namespace Fyre\Console;

use NumberFormatter;

use function array_is_list;
use function array_keys;
use function array_unshift;
use function count;
use function exec;
use function fgets;
use function fwrite;
use function implode;
use function max;
use function min;
use function preg_replace;
use function round;
use function rtrim;
use function str_pad;
use function str_repeat;
use function strcasecmp;
use function wordwrap;

use const PHP_EOL;
use const PHP_INT_MAX;
use const STDERR;
use const STDOUT;

/**
 * Console
 */
class Console
{
    public const BLACK = 30;

    public const BLUE = 34;

    public const BOLD = 1;

    public const CYAN = 36;

    public const DARKGRAY = 100;

    public const DIM = 2;

    public const FLASH = 5;

    public const GRAY = 47;

    public const GREEN = 32;

    public const ITALIC = 3;

    public const PURPLE = 35;

    public const RED = 31;

    public const UNDERLINE = 4;

    public const WHITE = 37;

    public const YELLOW = 33;

    protected const TOTAL_STEPS = 10;

    protected static NumberFormatter $percentFormatter;

    protected $error;

    protected $input;

    protected int|null $lastStep = null;

    protected $output;

    /**
     * Get the terminal height (in characters).
     *
     * @return int The terminal height.
     */
    public static function getHeight(): int
    {
        return (int) exec('tput lines');
    }

    /**
     * Get the terminal width (in characters).
     *
     * @return int The terminal width.
     */
    public static function getWidth(): int
    {
        return (int) exec('tput cols');
    }

    /**
     * Style a string for terminal output.
     *
     * @param string $text The text.
     * @param array $options The style options.
     * @return string The styled text.
     */
    public static function style(string $text, array $options = []): string
    {
        $style = $options['style'] ?? null;
        $color = $options['color'] ?? null;
        $bg = $options['bg'] ?? null;

        if (!$text || (!$color && !$bg && !$style)) {
            return $text;
        }

        $result = "\033[";
        $result .= (int) ($style ?? 0);
        $result .= ';';
        $result .= (int) ($color ?? static::WHITE);

        if ($bg !== null) {
            $result .= ';';
            $result .= (int) $bg + 10;
        }

        $result .= 'm';
        $result .= $text;
        $result .= "\033[0m";

        return $result;
    }

    /**
     * New Console constructor.
     *
     * @param resource $input The input stream.
     * @param resource $output The output stream.
     * @param resource $error The error stream.
     */
    public function __construct($input = STDIN, $output = STDOUT, $error = STDERR)
    {
        $this->input = $input;
        $this->output = $output;
        $this->error = $error;
    }

    /**
     * Prompt to make a choice out of available options.
     *
     * @param string $text The prompt text.
     * @param array $options The options.
     * @param string|null $default The default option.
     * @return string|null The selected option.
     */
    public function choice(string $text, array $options, string|null $default = null): string|null
    {
        $this->write($text, ['color' => static::YELLOW]);

        $prefix = '';
        if (!array_is_list($options)) {
            $optionKeys = array_keys($options);

            $maxLength = 0;
            foreach ($optionKeys as $option) {
                $maxLength = max($maxLength, strlen($option));
            }

            foreach ($options as $option => $description) {
                $key = str_pad('  ['.$option.']', $maxLength + 6);
                $key = static::style($key, ['color' => static::CYAN]);
                $value = static::style($description, ['style' => static::DIM]);

                $this->write($key.$value);
            }

            $prefix = static::style('Choice', ['color' => static::YELLOW]);
        } else {
            $optionKeys = $options;
        }

        $optionList = [];
        foreach ($optionKeys as $option) {
            $optionStyles = ['color' => static::CYAN];

            if ($option === $default) {
                $optionStyles['style'] = static::BOLD;
            } else {
                $optionStyles['style'] = static::DIM;
            }

            $optionList[] = static::style($option, $optionStyles);
        }

        $this->write($prefix.' ('.implode('/', $optionList).')');

        $choice = $this->input() ?: $default;

        foreach ($optionKeys as $option) {
            if (strcasecmp($option, $choice) === 0) {
                return $option;
            }
        }

        return $default;
    }

    /**
     * Output comment text.
     *
     * @param string $text The text.
     * @param array $options The style options.
     */
    public function comment(string $text, array $options = [])
    {
        $options['style'] ??= static::DIM;

        return $this->write($text, $options);
    }

    /**
     * Prompt the user to confirm (y/n).
     *
     * @param string $text The prompt text.
     * @param bool $default The default option.
     * @return bool TRUE if the user confirmed the prompt, otherwise FALSE.
     */
    public function confirm(string $text, bool $default = true): bool
    {
        $choice = $this->choice($text, ['y', 'n'], $default ? 'y' : 'n');

        return $choice === 'y';
    }

    /**
     * Output text to STDERR.
     *
     * @param string $text The text.
     * @param array $options The style options.
     */
    public function error(string $text, array $options = []): void
    {
        $options['color'] ??= static::RED;

        $text = static::style($text, $options);

        fwrite($this->error, $text.PHP_EOL);
    }

    /**
     * Output info text.
     *
     * @param string $text The text.
     * @param array $options The style options.
     */
    public function info(string $text, array $options = [])
    {
        $options['color'] ??= static::BLUE;

        return $this->write($text, $options);
    }

    /**
     * Read a line of input.
     *
     * @return string The input text.
     */
    public function input(): string
    {
        return rtrim(fgets($this->input), "\r\n");
    }

    /**
     * Output a progress indicator.
     *
     * @param int|null $step The step.
     * @param int $totalSteps The total steps.
     */
    public function progress(int|null $step = null, int $totalSteps = 10): void
    {
        if ($step === null) {
            $this->lastStep = $step;

            fwrite($this->output, "\033[1A\033[K");
            fwrite($this->output, "\007");

            return;
        }

        if ($this->lastStep && $this->lastStep <= $step) {
            fwrite($this->output, "\r\033[1A\r\033[K\r");
        }

        $this->lastStep = $step;

        $step = max($step, 1);
        $totalSteps = max($totalSteps, 1);

        $percent = $step / $totalSteps;
        $step = (int) round($percent * static::TOTAL_STEPS);

        $progressString = str_repeat('#', $step).
            str_repeat('.', static::TOTAL_STEPS - $step);

        $percentString = static::percentFormatter()->format($percent);

        $this->write('['.static::style($progressString, ['color' => static::GREEN]).'] '.$percentString);
    }

    /**
     * Prompt the user for input.
     *
     * @param string $text The prompt text.
     * @return string The input text.
     */
    public function prompt(string $text): string
    {
        $this->write($text, ['color' => static::YELLOW]);

        return $this->input();
    }

    /**
     * Output success text.
     *
     * @param string $text The text.
     * @param array $options The style options.
     */
    public function success(string $text, array $options = [])
    {
        $options['color'] ??= static::GREEN;

        return $this->write($text, $options);
    }

    /**
     * Output a table.
     *
     * @param array $data The table rows.
     * @param array $header The table header columns.
     */
    public function table(array $data, array $header = []): void
    {
        if ($header !== []) {
            array_unshift($data, $header);
        }

        $maxLengths = [];

        foreach ($data as $row) {
            foreach ($row as $i => $value) {
                $maxLengths[$i] ??= 0;
                $maxLengths[$i] = max($maxLengths[$i], static::strlen((string) $value));
            }
        }

        $border = '+';
        foreach ($maxLengths as $length) {
            $border .= str_repeat('-', $length + 2).'+';
        }
        $border .= PHP_EOL;

        foreach ($data as $i => $row) {
            foreach ($row as $j => $value) {
                $diff = $maxLengths[$j] - static::strlen((string) $value);
                $data[$i][$j] .= str_repeat(' ', $diff);
            }
        }

        $rowCount = count($data);

        $table = '';

        foreach ($data as $i => $row) {
            if ($i === 0) {
                $table .= $border;
            }

            $table .= '| '.implode(' | ', $row).' |'.PHP_EOL;

            if (($i === 0 && $header !== []) || $i === $rowCount - 1) {
                $table .= $border;
            }
        }

        fwrite($this->output, $table);
    }

    /**
     * Output warning text.
     *
     * @param string $text The text.
     * @param array $options The style options.
     */
    public function warning(string $text, array $options = [])
    {
        $options['color'] ??= static::YELLOW;

        return $this->write($text, $options);
    }

    /**
     * Wrap text for terminal output.
     *
     * @param string $text The text.
     * @param int|null $maxWidth The maximum width.
     */
    public function wrap(string $text, int|null $maxWidth = null): string
    {
        $maxWidth = min($maxWidth ?? PHP_INT_MAX, static::getWidth());

        return wordwrap($text, $maxWidth, PHP_EOL);
    }

    /**
     * Output text to STDOUT.
     *
     * @param string $text The text.
     * @param array $options The style options.
     */
    public function write(string $text, array $options = []): void
    {
        $text = static::style($text, $options);

        fwrite($this->output, $text.PHP_EOL);
    }

    /**
     * Create a percent formatter.
     *
     * @return NumberFormatter The percent formatter.
     */
    protected static function percentFormatter(): NumberFormatter
    {
        return static::$percentFormatter ??= new NumberFormatter('en_US', NumberFormatter::PERCENT);
    }

    /**
     * Get the real length of a string.
     *
     * @param string $string The string.
     * @return int The length.
     */
    protected static function strlen(string $string): int
    {
        $string = preg_replace('/\\033\[[\d;]+?m/', '', $string);

        return mb_strwidth($string);
    }
}
