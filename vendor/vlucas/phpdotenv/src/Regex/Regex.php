<?php

namespace MolliePrefix\Dotenv\Regex;

use MolliePrefix\PhpOption\Option;
class Regex
{
    /**
     * Perform a preg replace, wrapping up the result.
     *
     * @param string $pattern
     * @param string $replacement
     * @param string $subject
     *
     * @return \Dotenv\Regex\Result
     */
    public static function replace($pattern, $replacement, $subject)
    {
        return self::pregAndWrap(function ($subject) use($pattern, $replacement) {
            return \preg_replace($pattern, $replacement, $subject);
        }, $subject);
    }
    /**
     * Perform a preg replace callback, wrapping up the result.
     *
     * @param string   $pattern
     * @param callable $callback
     * @param string   $subject
     *
     * @return \Dotenv\Regex\Result
     */
    public static function replaceCallback($pattern, callable $callback, $subject)
    {
        return self::pregAndWrap(function ($subject) use($pattern, $callback) {
            return \preg_replace_callback($pattern, $callback, $subject);
        }, $subject);
    }
    /**
     * Perform a preg operation, wrapping up the result.
     *
     * @param callable $operation
     * @param string   $subject
     *
     * @return \Dotenv\Regex\Result
     */
    private static function pregAndWrap(callable $operation, $subject)
    {
        $result = (string) @$operation($subject);
        if (($e = \preg_last_error()) !== \PREG_NO_ERROR) {
            return \MolliePrefix\Dotenv\Regex\Error::create(self::lookupError($e));
        }
        return \MolliePrefix\Dotenv\Regex\Success::create($result);
    }
    /**
     * Lookup the preg error code.
     *
     * @param int $code
     *
     * @return string
     */
    private static function lookupError($code)
    {
        return \MolliePrefix\PhpOption\Option::fromValue(\get_defined_constants(\true))->filter(function (array $consts) {
            return isset($consts['pcre']) && \defined('ARRAY_FILTER_USE_KEY');
        })->map(function (array $consts) {
            return \array_filter($consts['pcre'], function ($msg) {
                return \substr($msg, -6) === '_ERROR';
            }, \ARRAY_FILTER_USE_KEY);
        })->flatMap(function (array $errors) use($code) {
            return \MolliePrefix\PhpOption\Option::fromValue(\array_search($code, $errors, \true));
        })->getOrElse('PREG_ERROR');
    }
}
