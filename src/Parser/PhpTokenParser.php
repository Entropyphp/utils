<?php

declare(strict_types=1);

namespace Entropy\Utils\Parser;

use function count;
use function defined;
use function in_array;
use function is_array;
use function token_get_all;

use const T_CLASS;
use const T_DOUBLE_COLON;
use const T_NAMESPACE;
use const T_NS_SEPARATOR;
use const T_STRING;

class PhpTokenParser
{
    /**
     * Returns the full class name for the first class in the file.
     *
     * @param string $content to parse
     * @return string|false Full class name if found, false otherwise
     */
    public static function findClass(string $content): bool|string
    {

        $class = false;
        $namespace = false;
        $doubleColon = false;
        $tokens = token_get_all($content);

        $nsToken = [T_NS_SEPARATOR, T_STRING];
        if (PHP_VERSION_ID >= 80000) {
            if (defined('T_NAME_QUALIFIED')) {
                $nsToken[] = T_NAME_QUALIFIED;
            }
            if (defined('T_NAME_FULLY_QUALIFIED')) {
                $nsToken[] = T_NAME_FULLY_QUALIFIED;
            }
        }

        $skipToken = [T_DOC_COMMENT, T_WHITESPACE, T_COMMENT];

        for ($i = 0, $count = count($tokens); $i < $count; $i++) {
            $token = $tokens[$i];

            if (!is_array($token)) {
                continue;
            }

            if (true === $doubleColon) {
                $doubleColon = false;
                do {
                    if (T_CLASS === $token[0]) {
                        $doubleColon = true;
                        break;
                    } elseif (!in_array($token[0], $skipToken, true)) {
                        break;
                    }
                    $token = $tokens[++$i];
                } while ($i < $count && is_array($token));
            }

            if (true === $class && T_STRING === $token[0]) {
                return $namespace . '\\' . $token[1];
            }

            if (true === $namespace && in_array($token[0], $nsToken)) {
                $namespace = '';
                do {
                    $namespace .= $token[1];
                    $token = $tokens[++$i];
                } while ($i < $count && is_array($token) && in_array($token[0], $nsToken));
            }
            if (T_DOUBLE_COLON === $token[0]) {
                $doubleColon = true;
            }
            if (T_CLASS === $token[0]) {
                if ($doubleColon === false) {
                    $class = true;
                }
                $doubleColon = false;
            }
            if (T_NAMESPACE === $token[0]) {
                $namespace = true;
            }
        }
        return false;
    }
}
