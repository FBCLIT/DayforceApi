<?php

namespace Fbclit\DayforceApi;

use Exception;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\ClientException;

class ApiException extends Exception
{
    use DecodesJsonResponse;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * Constructor.
     *
     * @param string            $message
     * @param ResponseInterface $response
     * @param Exception|null    $previous
     */
    public function __construct($message, ResponseInterface $response, Exception $previous = null)
    {
        parent::__construct($message, $response->getStatusCode(), $previous);

        $this->response = $response;
    }

    /**
     * Create a new API exception from the given client exception.
     *
     * @param ClientException $e
     *
     * @return ApiException
     */
    public static function fromClientException(ClientException $e)
    {
        return new ApiException($e->getMessage(), $e->getResponse(), $e);
    }

    /**
     * Get the process results from the client exception.
     *
     * @return array
     */
    public function getProcessResults()
    {
        return $this->decodeResponse($this->response)['processResults'] ?? [];
    }
}
