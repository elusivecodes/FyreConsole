# FyreConsole

**FyreConsole** is a free, open-source CLI library for *PHP*.


## Table Of Contents
- [Installation](#installation)
- [Methods](#methods)
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


## Methods

**Choice**

Prompt to make a choice out of available options.

- `$text` is a string representing the prompt text.
- `$options` is an array containing the options.
- `$default` is a string representing the default option, and will default to *null*.

```php
$choice = Console::choice($text, $options, $default);
```

**Comment**

Output comment text.

- `$text` is a string representing the text.
- `$options` is an array containing the color options.
    - `color` is a number representing the text [color](#colors), and will default to *null*.
    - `bg` is a number representing the background [color](#colors), and will default to *null*.
    - `style` is a number indicating the text [style](#styles), and will default to `Console::DIM`.

```php
Console::comment($text);
```

**Confirm**

Prompt to confirm with y/n options.

- `$text` is a string representing the prompt text.
- `$default` is a boolean representing the default option, and will default to *true*.

```php
$confirm = Console::confirm($text, $default);
```

**Error**

Output text to *STDERR*.

- `$text` is a string representing the text.
- `$options` is an array containing the color options.
    - `color` is a number representing the text [color](#colors), and will default to `Console::RED`.
    - `bg` is a number representing the background [color](#colors), and will default to *null*.
    - `style` is a number indicating the text [style](#styles), and will default to *null*.

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

**Info**

Output info text.

- `$text` is a string representing the text.
- `$options` is an array containing the color options.
    - `color` is a number representing the text [color](#colors), and will default to `Console::BLUE`.
    - `bg` is a number representing the background [color](#colors), and will default to *null*.
    - `style` is a number indicating the text [style](#styles), and will default to *null*.

```php
Console::info($text, $options);
```

**Input**

Read a line of input.

```php
$input = Console::input();
```

**Progress**

Output a progress indicator.

- `$step` is a number representing the current step, and will default to *null*.
- `$totalSteps` is a number representing the total steps, and will default to *10*.

```php
Console::progress($step, $totalSteps);
```

Sequential calls to this method will update the progress indicator. If the `$step` is set to *null* the indicator will be cleared.

**Prompt**

Prompt the user for input.

- `$text` is a string representing the prompt text.

```php
$prompt = Console::prompt($text);
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

**Success**

Output success text.

- `$text` is a string representing the text.
- `$options` is an array containing the color options.
    - `color` is a number representing the text [color](#colors), and will default to `Console::GREEN`.
    - `bg` is a number representing the background [color](#colors), and will default to *null*.
    - `style` is a number indicating the text [style](#styles), and will default to *null*.

```php
Console::success($text, $options);
```

**Table**

Output a table.

- `$data` is an array containing the table rows.
- `$header` is an array containing the table header columns, and will default to *[]*.

```php
Console::table($data, $header);
```

**Warning**

Output warning text.

- `$text` is a string representing the text.
- `$options` is an array containing the color options.
    - `color` is a number representing the text [color](#colors), and will default to `Console::YELLOW`.
    - `bg` is a number representing the background [color](#colors), and will default to *null*.
    - `style` is a number indicating the text [style](#styles), and will default to *null*.

```php
Console::warning($text, $options);
```

**Wrap**

Wrap text for terminal output.

- `$text` is a string representing the text.
- `$maxWidth` is a number representing the maximum character width of a line, and will default to the terminal width.

```php
$wrap = Console::wrap($text, $maxWidth);
```

**Write**

Output text to *STDOUT*.

- `$text` is a string representing the text.
- `$options` is an array containing the color options.
    - `color` is a number representing the text [color](#colors), and will default to `Console::YELLOW`.
    - `bg` is a number representing the background [color](#colors), and will default to *null*.
    - `style` is a number indicating the text [style](#styles), and will default to *null*.

```php
Console::write($text, $options);
```


## Colors

```php
Console::BLACK;
Console::RED;
Console::GREEN;
Console::YELLOW;
Console::BLUE;
Console::PURPLE;
Console::CYAN;
Console::WHITE;
Console::GRAY;
Console::DARKGRAY;
```


## Styles

```php
Console::BOLD;
Console::DIM;
Console::ITALIC;
Console::UNDERLINE;
Console::FLASH;
```
