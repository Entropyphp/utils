<?php

namespace Entropy\Utils\Environnement;

use function mb_substr;
use function is_string;
use function getenv;

class Env
{
    /**
     * return environnement variable if is set else default value or null
     *
     * @param string $var
     * @param string|null $default
     * @return string|null
     * @todo Add probably base64_decode if needed
     */
    public static function getEnv(string $var, ?string $default = null): ?string
    {
        $env = getenv($var, true) ?: getenv($var);
        if ($env === false) {
            return $default;
        }

        if (is_string($env) && 'base64:' === mb_substr($env, 0, 7)) {
            $env = mb_substr($env, 7);
        }
        return $env;
    }
}
