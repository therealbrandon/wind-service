<?php

namespace Tests\Unit;

use App\Services\WindService;
use App\Exceptions\WindServiceException;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7;
use Tests\TestCase;

class WindServiceTest extends TestCase
{
    private $testZipCode;
    private $status;
    private $headers;
    private $body;
    private $windService;

    public function setUp()
    {
        parent::setUp();

        $this->testZipCode = 89101;
        $this->status = 200;
        $this->headers = ['Content-Type' => 'application/json'];
    }

    private function setClientResponseBody(string $body = null)
    {
        if (is_null($body)) {
            $body = Psr7\stream_for(
                '{
                  "coord": {
                    "lon": -115.12,
                    "lat": 36.13
                  },
                  "weather": [
                    {
                      "id": 803,
                      "main": "Clouds",
                      "description": "broken clouds",
                      "icon": "04d"
                    }
                  ],
                  "base": "stations",
                  "main": {
                    "temp": 298.86,
                    "pressure": 1016,
                    "humidity": 22,
                    "temp_min": 297.75,
                    "temp_max": 299.85
                  },
                  "visibility": 16093,
                  "wind": {
                    "speed": 4.5,
                    "deg": 72.3
                  },
                  "clouds": {
                    "all": 75
                  },
                  "dt": 1541199360,
                  "sys": {
                    "type": 1,
                    "id": 2061,
                    "message": 0.0047,
                    "country": "US",
                    "sunrise": 1541167514,
                    "sunset": 1541205739
                  },
                  "id": 420025239,
                  "name": "Las Vegas",
                  "cod": 200
                }'
            );
        }

        $this->body = $body;

        $mock = new MockHandler([
            new Response($this->status, $this->headers, $this->body)
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $this->windService = new WindService($client);
    }

    public function test_hitting_the_external_api_sucessfully_returns_mutated_result()
    {
        $this->setClientResponseBody(null);

        $expected = '{"speed":4.5,"direction":"ENE"}';
        $actual = $this->windService->getWindByZip($this->testZipCode);

        $this->assertJsonStringEqualsJsonString($expected, $actual);
    }

    public function test_passing_an_invalid_zipcode_throws_exception()
    {
        $this->expectException(WindServiceException::class);

        $this->setClientResponseBody(null);
        $this->windService->getWindByZip(123);
    }

    public function test_an_empty_json_response_from_openweathermaps_throws_exception()
    {
        $this->expectException(WindServiceException::class);

        $this->setClientResponseBody('');
        $this->windService->getWindByZip($this->testZipCode);
    }

    public function test_a_malformed_json_response_from_openweathermaps_throws_exception()
    {
        $this->expectException(WindServiceException::class);

        $this->setClientResponseBody('malformed json');
        $this->windService->getWindByZip($this->testZipCode);
    }

    public function test_a_response_from_openweathermaps_is_missing_wind_data_throws_exception()
    {
        $this->expectException(WindServiceException::class);

        $body = Psr7\stream_for(
            '{
              "coord": {
                "lon": -115.12,
                "lat": 36.13
              },
              "weather": [
                {
                  "id": 803,
                  "main": "Clouds",
                  "description": "broken clouds",
                  "icon": "04d"
                }
              ],
              "base": "stations",
              "main": {
                "temp": 298.86,
                "pressure": 1016,
                "humidity": 22,
                "temp_min": 297.75,
                "temp_max": 299.85
              },
              "visibility": 16093,
              "clouds": {
                "all": 75
              },
              "dt": 1541199360,
              "sys": {
                "type": 1,
                "id": 2061,
                "message": 0.0047,
                "country": "US",
                "sunrise": 1541167514,
                "sunset": 1541205739
              },
              "id": 420025239,
              "name": "Las Vegas",
              "cod": 200
            }'
        );
        $this->setClientResponseBody($body);

        $this->windService->getWindByZip($this->testZipCode);
    }
}
