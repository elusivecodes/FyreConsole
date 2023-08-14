<?php

namespace Tests\Mock;

use php_user_filter;

use const PSFS_PASS_ON;

use function stream_bucket_make_writeable;
use function stream_filter_register;

class StreamFilter extends php_user_filter
{

    protected static string $buffer = '';

    public function filter($in, $out, &$consumed, $closing): int
    {
        while ($bucket = stream_bucket_make_writeable($in)) {
            static::$buffer .= $bucket->data;
            $consumed += $bucket->datalen;
        }

        return PSFS_PASS_ON;
    }

    public static function clear(): void
    {
        static::$buffer = '';
    }

    public static function getBuffer(): string
    {
        return static::$buffer;
    }

    public static function register(): void
    {
        stream_filter_register('StreamFilter', static::class);
    }

}
