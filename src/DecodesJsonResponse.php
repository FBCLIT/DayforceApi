<?php

namespace Fbclit\DayforceApi;

use Psr\Http\Message\ResponseInterface;

trait DecodesJsonResponse
{
    /**
     * Decode the given response content into an associative array.
     *
     * @param ResponseInterface $response
     *
     * @return array
     */
    protected function decodeResponse(ResponseInterface $response)
    {
        return json_decode($response->getBody()->getContents(), $assoc = true);
    }
}
