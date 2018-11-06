<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use JMS\Serializer\Annotation as JMS;

/**
 *
 * @JMS\ExclusionPolicy("none")
 */
class Weather extends Model
{
    /**
     * @JMS\Type("array<string, float>")
     */
    public $wind;

    // Getters
    public function getWind()
    {
        return $this->wind;
    }

    public function getWindSpeed()
    {
        if (array_key_exists('speed', $this->wind)) {
            return $this->wind['speed'];
        }
        return null;
    }

    public function getWindDegree()
    {
        if (array_key_exists('deg', $this->wind)) {
            return $this->wind['deg'];
        }
        return null;
    }
}
