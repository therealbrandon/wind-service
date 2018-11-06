<?php

namespace App\Services;

use App\Weather;
use App\Exceptions\WindServiceException;
use App\Interfaces\WindInterface;
use Doctrine\Common\Annotations\AnnotationRegistry;
use GuzzleHttp\Client;
use JMS\Serializer\Exception\RuntimeException as JMSException;
use JMS\Serializer\SerializerBuilder;
use Illuminate\Http\Request;

class WindService implements WindInterface
{
    private $guzzle;
    private $appid;

    public function __construct(Client $guzzle)
    {
        $this->guzzle = $guzzle;
        $this->appid = '9b6155f4cfc40ccab95b6a747120dd42';
    }

    public function getWindByZip($zipCode)
    {
        if (!preg_match('/\b\d{5}\b/', $zipCode)) {
            throw new WindServiceException('Invalid zip code.');
        };

        $url = "http://api.openweathermap.org/data/2.5/weather?zip=$zipCode,us&units=imperial&appid=$this->appid";
        $json = $this->sendRequest($url);

        if (empty($json)) {
            throw new WindServiceException('No JSON response from OpenWeatherMap.');
        }

        $serializer = $this->buildSerializer();

        try {
            $data = $serializer->deserialize($json, 'App\Weather', 'json');
        } catch (JMSException $exception) {
            throw new WindServiceException('Malformed JSON response from OpenWeatherMap.');
        }

        if (is_null($data->getWind())) {
            throw new WindServiceException('Missing wind data from OpenWeatherMap.');
        }

        return $serializer->serialize($this->mutateData($data), 'json');
    }

    private function buildSerializer()
    {
        AnnotationRegistry::registerLoader('class_exists');
        return SerializerBuilder::create()->build();
    }

    private function mutateData(Weather $data)
    {
        $wind = array();
        $wind['speed'] = $data->getWindSpeed(); // mph
        $degree = $data->getWindDegree();
        if (!is_null($degree)) {
            $wind['direction'] = $this->wind_cardinals($degree);
        }

        return $wind;
    }

    private function sendRequest(string $url)
    {
        $response = $this->guzzle->request('GET', $url);
        return $response->getBody()->getContents();
    }

    private function wind_cardinals(float $degree) {
        $cardinalDirections = array(
            'N' => array(348.75, 360),
            'N2' => array(0, 11.25),
            'NNE' => array(11.25, 33.75),
            'NE' => array(33.75, 56.25),
            'ENE' => array(56.25, 78.75),
            'E' => array(78.75, 101.25),
            'ESE' => array(101.25, 123.75),
            'SE' => array(123.75, 146.25),
            'SSE' => array(146.25, 168.75),
            'S' => array(168.75, 191.25),
            'SSW' => array(191.25, 213.75),
            'SW' => array(213.75, 236.25),
            'WSW' => array(236.25, 258.75),
            'W' => array(258.75, 281.25),
            'WNW' => array(281.25, 303.75),
            'NW' => array(303.75, 326.25),
            'NNW' => array(326.25, 348.75)
        );

        foreach ($cardinalDirections as $direction => $angles) {
            if ($degree >= $angles[0] && $degree < $angles[1]) {
                $cardinal = str_replace("2", "", $direction);
            }
        }

        return $cardinal;
    }
}
