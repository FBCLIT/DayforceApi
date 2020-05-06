# Dayforce PHP API

## Requirements

- PHP >= 7.2
- PHP JSON Extension enabled

## Installation

```bash
composer require fbclit/dayforceapi
```

## Usage

```php
use Fbclit\DayforceApi\Client;
use Fbclit\DayforceApi\ApiException;
use Fbclit\DayforceApi\NoDataException;

$url = 'http://usconfigr58.dayforcehcm.com';
$company = 'acme';

$client = new Client($url, $company);

$api = $client->connect('username', 'secret');

try {
    $employees = $api->employees();
    
    foreach ($employees as $employee) {
        // XRefCode:
        echo $employee;
    }
} catch (NoDataException $e) {
    // No data was returned in the API request.
    foreach ($e->getProcessResults() as $error) {
        //
    }
} catch (ApiException $e) {
    // An HTTP exception or invalid credentials.

    // GuzzleHttp\Exception\ClientException
    $previous = $e->getPrevious();
}
```
