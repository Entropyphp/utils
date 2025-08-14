<?php

declare(strict_types=1);

namespace Entropy\Utils\HttpUtils;

use GuzzleHttp\Psr7\Response;

class RedirectResponse extends Response
{
    /**
     * ResponseRedirect constructor.
     * @param string $url
     */
    public function __construct(string $url)
    {
        parent::__construct(301, ['Location' => $url]);
    }
}
