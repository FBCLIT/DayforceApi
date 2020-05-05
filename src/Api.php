<?php

namespace Fbclit\DayforceApi;

use DateTime;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;

class Api
{
    use DecodesJsonResponse;

    /**
     * The underyling Guzzle HTTP client.
     *
     * @var ClientInterface
     */
    protected $client;

    /**
     * The date format to use when passing dates in query parameters.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d\TH:i:s';

    /**
     * Constructor.
     *
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Get a listing of all Employees XRefCode's
     *
     * @param array $params
     *
     * @return array
     */
    public function employees(...$params)
    {
        return array_map(function ($data) {
            return $data['XRefCode'];
        }, $this->get('Employees', ['query' => $params]));
    }

    /**
     * Get employee details by their XRefCode.
     *
     * @param string $xRefCode
     *
     * @return array
     */
    public function employeeDetails($xRefCode)
    {
        return $this->get("Employees/$xRefCode");
    }

    /**
     * Get the employee's availability.
     *
     * @param string $xRefCode
     *
     * @return array
     */
    public function employeeAvailability($xRefCode)
    {
        return $this->get("Employees/$xRefCode/Availability");
    }

    /**
     * Get the employee's schedule from the start date to end date.
     *
     * @param string   $xRefCode
     * @param DateTime $startDate
     * @param DateTime $endDate
     *
     * @throws NoDataException
     *
     * @return array
     */
    public function employeeSchedules($xRefCode, DateTime $startDate, DateTime $endDate)
    {
        return $this->get("Employees/$xRefCode/Schedules", [
            'query' => [
                'filterScheduleStartDate' => $startDate->format($this->dateFormat),
                'filterScheduleEndDate' => $endDate->format($this->dateFormat),
            ],
        ]);
    }

    /**
     * Get a listing of employee's time away from work entries.
     *
     * @param string $xRefCode
     *
     * @return array
     */
    public function employeeTimeAway($xRefCode)
    {
        return $this->get("Employees/$xRefCode/TimeAwayFromWork");
    }

    /**
     * Send a GET request to the OpenText API.
     *
     * @param string $url
     * @param array  $options
     *
     * @throws ApiException|NoDataException
     *
     * @return mixed
     */
    protected function get($url, array $options = [])
    {
        try {
            $response = $this->client->get($url, $options);

            $body = $this->decodeResponse($response);

            if (isset($body['Data'])) {
                return $body['Data'];
            }

            throw NoDataException::fromResponse($response);
        } catch (ClientException $e) {
            throw ApiException::fromClientException($e);
        }
    }

    /**
     * Send a POST request to the OpenText API.
     *
     * @param string $url
     * @param array  $options
     *
     * @return mixed
     */
    protected function post($url, array $options = [])
    {
        return $this->decodeResponse(
            $this->client->post($url, $options)
        );
    }
}
