# FyreConsole

**FyreConsole** is a free, open-source CLI library for *PHP*.


## Table Of Contents
- [Installation](#installation)
- [Basic Usage](#basic-usage)
- [Methods](#methods)
- [Static Methods](#static-methods)
- [Colors](#colors)
- [Styles](#styles)



## Installation

**Using Composer**

```
composer require fyre/console
```

In PHP:

```php
use Fyre\Console\Console;
```


## Basic Usage

- `$input` is the input stream, and will default to `STDIN`.
- `$output` is the output stream, and will default to `STDOUT`.
- `$error` is the error stream, and will default to `STDERR`.

```php
$io = new Console($input, $output, $error);
```


## Methods

**Choice**

Prompt to make a choice out of available options.

- `$text` is a string representing the prompt text.
- `$options` is an array containing the options.
- `$default` is a string representing the default option, and will default to *null*.

```php
$choice = $io->choice($text, $options, $default);
```

**Comment**

Output comment text.

- `$text` is a string representing the text.
- `$options` is an array containing the color options.
    - `color` is a number representing the text [color](#colors), and will default to *null*.
    - `bg` is a number representing the background [color](#colors), and will default to *null*.
    - `style` is a number indicating the text [style](#styles), and will default to `Console::DIM`.

```php
$io->comment($text);
```

**Confirm**

Prompt to confirm with y/n options.

- `$text` is a string representing the prompt text.
- `$default` is a boolean representing the default option, and will default to *true*.

```php
$confirm = $io->confirm($text, $default);
```

**Error**

Output text to *STDERR*.

- `$text` is a string representing the text.
- `$options` is an array containing the color options.
    - `color` is a number representing the text [color](#colors), and will default to `Console::RED`.
    - `bg` is a number representing the background [color](#colors), and will default to *null*.
    - `style` is a number indicating the text [style](#styles), and will default to *null*.

```php
$io->error($text, $options);
```

**Info**

Output info text.

- `$text` is a string representing the text.
- `$options` is an array containing the color options.
    - `color` is a number representing the text [color](#colors), and will default to `Console::BLUE`.
    - `bg` is a number representing the background [color](#colors), and will default to *null*.
    - `style` is a number indicating the text [style](#styles), and will default to *null*.

```php
$io->info($text, $options);
```

**Input**

Read a line of input.

```php
$input = $io->input();
```

**Progress**

Output a progress indicator.

- `$step` is a number representing the current step, and will default to *null*.
- `$totalSteps` is a number representing the total steps, and will default to *10*.

```php
$io->progress($step, $totalSteps);
```

Sequential calls to this method will update the progress indicator. If the `$step` is set to *null* the indicator will be cleared.

**Prompt**

Prompt the user for input.

- `$text` is a string representing the prompt text.

```php
$prompt = $io->prompt($text);
```

**Success**

Output success text.

- `$text` is a string representing the text.
- `$options` is an array containing the color options.
    - `color` is a number representing the text [color](#colors), and will default to `Console::GREEN`.
    - `bg` is a number representing the background [color](#colors), and will default to *null*.
    - `style` is a number indicating the text [style](#styles), and will default to *null*.

```php
$io->success($text, $options);
```

**Table**

Output a table.

- `$data` is an array containing the table rows.
- `$header` is an array containing the table header columns, and will default to *[]*.

```php
$io->table($data, $header);
```

**Warning**

Output warning text.

- `$text` is a string representing the text.
- `$options` is an array containing the color options.
    - `color` is a number representing the text [color](#colors), and will default to `Console::YELLOW`.
    - `bg` is a number representing the background [color](#colors), and will default to *null*.
    - `style` is a number indicating the text [style](#styles), and will default to *null*.

```php
$io->warning($text, $options);
```

**Wrap**

Wrap text for terminal output.

- `$text` is a string representing the text.
- `$maxWidth` is a number representing the maximum character width of a line, and will default to the terminal width.

```php
$wrap = $io->wrap($text, $maxWidth);
```

**Write**

Output text to *STDOUT*.

- `$text` is a string representing the text.
- `$options` is an array containing the color options.
    - `color` is a number representing the text [color](#colors), and will default to `Console::YELLOW`.
    - `bg` is a number representing the background [color](#colors), and will default to *null*.
    - `style` is a number indicating the text [style](#styles), and will default to *null*.

```php
$io->write($text, $options);
```


## Static Methods

**Get Height**

Get the terminal height (in characters).

```php
$height = Console::getHeight();
```

**Get Width**

Get the terminal width (in characters).

```php
$width = Console::getWidth();
```

**Style**

Style text for terminal output.

- `$text` is a string representing the text.
- `$options` is an array containing the color options.
    - `color` is a number representing the text [color](#colors), and will default to *null*.
    - `bg` is a number representing the background [color](#colors), and will default to *null*.
    - `style` is a number indicating the text [style](#styles), and will default to *null*.

```php
$style = Console::style($text, $options);
```


## Colors

```php
Console::BLACK; // 30
Console::RED; // 31
Console::GREEN; // 32
Console::YELLOW; // 33
Console::BLUE; // 34
Console::PURPLE; // 35
Console::CYAN; // 36
Console::WHITE; // 37
Console::GRAY; // 47
Console::DARKGRAY; // 100
```


## Styles

```php
Console::BOLD; // 1
Console::DIM; // 2
Console::ITALIC; // 3
Console::UNDERLINE; // 4
Console::FLASH; // 5
```
