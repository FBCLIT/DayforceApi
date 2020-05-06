<?php

namespace Fbclit\DayforceApi;

use Closure;
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
     * Get a listing of all employee's XRefCodes.
     *
     * @param array $params
     *
     * @throws ApiException|NoDataException
     *
     * @return array
     */
    public function getEmployees(...$params)
    {
        return array_map(function ($data) {
            return $data['XRefCode'];
        }, $this->get('Employees', ['query' => $params]));
    }

    /**
     * Get a list of the employee's addresses.
     *
     * @param string $xRefCode
     *
     * @throws ApiException|NoDataException
     *
     * @return array
     */
    public function getEmployeeAddresses($xRefCode)
    {
        return $this->get("Employees/$xRefCode/Addresses");
    }

    /**
     * Get a list of the employees contacts.
     *
     * @param string $xRefCode
     *
     * @throws ApiException|NoDataException
     *
     * @return array
     */
    public function getEmployeeContacts($xRefCode)
    {
        return $this->get("Employees/$xRefCode/Contacts");
    }

    /**
     * Get the employee's details.
     *
     * @param string $xRefCode
     *
     * @throws ApiException|NoDataException
     *
     * @return array
     */
    public function getEmployeeDetails($xRefCode)
    {
        return $this->get("Employees/$xRefCode");
    }

    /**
     * Get the employee's availability.
     *
     * @param string $xRefCode
     *
     * @throws ApiException|NoDataException
     *
     * @return array
     */
    public function getEmployeeAvailability($xRefCode)
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
     * @throws ApiException|NoDataException
     *
     * @return array
     */
    public function getEmployeeSchedules($xRefCode, DateTime $startDate, DateTime $endDate)
    {
        return $this->get("Employees/$xRefCode/Schedules", [
            'query' => [
                'filterScheduleStartDate' => $startDate->format($this->dateFormat),
                'filterScheduleEndDate' => $endDate->format($this->dateFormat),
            ],
        ]);
    }

    /**
     * Get the employee's compensation summary.
     *
     * @param string $xRefCode
     *
     * @throws ApiException|NoDataException
     *
     * @return array
     */
    public function getEmployeeCompensation($xRefCode)
    {
        return $this->get("Employees/$xRefCode/CompensationSummary");
    }

    /**
     * Get a listing of employee's time away from work entries.
     *
     * @param string $xRefCode
     *
     * @return array
     */
    public function getEmployeeTimeAway($xRefCode)
    {
        return $this->get("Employees/$xRefCode/TimeAwayFromWork");
    }

    /**
     * Get a list of report metadata.
     *
     * @param string|null $xRefCode
     *
     * @return array
     */
    public function getReportMeta($xRefCode = null)
    {
        return $xRefCode
            ? $this->get("ReportMetadata/$xRefCode")
            : $this->get('ReportMetadata');
    }

    /**
     * Send a GET request to the Dayforce API.
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
        return $this->attempt(function () use ($url, $options) {
            $response = $this->client->get($url, $options);

            $body = $this->decodeResponse($response);

            if (isset($body['Data'])) {
                return $body['Data'];
            }

            throw NoDataException::fromResponse($response);
        });
    }

    /**
     * Send a POST request to the Dayforce API.
     *
     * @param string $url
     * @param array  $options
     *
     * @throws ApiException
     *
     * @return mixed
     */
    protected function post($url, array $options = [])
    {
        return $this->attempt(function () use ($url, $options) {
            return $this->decodeResponse(
                $this->client->post($url, $options)
            );
        });
    }

    /**
     * Attempt the HTTP API operation and return the result.
     *
     * @param Closure $operation
     *
     * @throws ApiException
     *
     * @return mixed
     */
    protected function attempt(Closure $operation)
    {
        try {
            return $operation();
        } catch (ClientException $e) {
            throw ApiException::fromClientException($e);
        }
    }
}
