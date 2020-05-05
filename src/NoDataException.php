<?php

namespace Fbclit\DayforceApi;

use Psr\Http\Message\ResponseInterface;

class NoDataException extends ApiException
{
    use DecodesJsonResponse;

    /**
     * Create a new exception from the given response.
     *
     * @param ResponseInterface $response
     *
     * @return static
     */
    public static function fromResponse(ResponseInterface $response)
    {
        return new static('No data returned in Dayforce API response.', $response);
    }
}
