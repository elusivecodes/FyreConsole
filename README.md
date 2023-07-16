# FyreConsole

**FyreConsole** is a free, open-source CLI library for *PHP*.


## Table Of Contents
- [Installation](#installation)
- [Methods](#methods)
- [Colors](#colors)
    - [Foreground](#foreground)
    - [Background](#background)



## Installation

**Using Composer**

```
composer require fyre/console
```

In PHP:

```php
use Fyre\Console\Console;
```


## Methods

**Color**

Color a string for terminal output, preserving existing colors.

- `$text` is a string representing the text.
- `$options` is an array containing the color options.
    - `foreground` is a string representing the [foreground color](#foreground).
    - `background` is a string representing the [background color](#background).
    - `underline` is a boolean indicating whether to underline the text, and will default to *false*.

```php
Console::color($text, $options);
```

**Error**

Output text to *STDERR*.

- `$text` is a string representing the text.
- `$options` is an array containing the color options.
    - `foreground` is a string representing the [foreground color](#foreground), and will default to "*light_red*".
    - `background` is a string representing the [background color](#background).
    - `underline` is a boolean indicating whether to underline the text, and will default to *false*.

```php
Console::error($text, $options);
```

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

**Input**

Read a line of input.

- `$prefix` is a string representing the prefix, and will default to "".

```php
$input = Console::input($prefix);
```

**Progress**

Output a progress indicator.

- `$step` is a number representing the current step, and will default to *null*.
- `$totalSteps` is a number representing the total steps, and will default to *10*.

```php
Console::progress($step, $totalSteps);
```

Sequential calls to this method will update the progress indicator. If the `$step` is set to *null* the indicator will be cleared.

**Table**

Output a table.

- `$data` is an array containing the table rows.
- `$header` is an array containing the table header columns, and will default to *[]*.

```php
Console::table($data, $header);
```

**Write**

Output text to STDOUT.

- `$text` is a string representing the text.
- `$options` is an array containing the color options.
    - `foreground` is a string representing the [foreground color](#foreground).
    - `background` is a string representing the [background color](#background).
    - `underline` is a boolean indicating whether to underline the text, and will default to *false*.

```php
Console::write($text, $options);
```


## Colors

### Foreground

- "*black*"
- "*red*"
- "*green*"
- "*yellow*"
- "*blue*"
- "*purple*"
- "*cyan*"
- "*light_gray*"
- "*dark_gray*"
- "*light_red*"
- "*light_green*"
- "*light_yellow*"
- "*light_blue*"
- "*light_purple*"
- "*light_cyan*"
- "*white*"

### Background

- "*black*"
- "*red*"
- "*green*"
- "*yellow*"
- "*blue*"
- "*purple*"
- "*cyan*"
- "*light_gray*"