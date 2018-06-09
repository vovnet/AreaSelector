<?php

namespace app\models;

use Yii;
use yii\base\Model;

class Areas extends Model {

  const EARTH_RADIUS = 6372795;
  
  public $areaNames;

  private $areas;

  
  public function __construct() {
    $this->areas = require __DIR__.'/data/areas.php';
    $this->areaNames = array_keys($this->areas);
  }

  public function getAreasSortedByName() {
    ksort($this->areas);
    return $this->areas;
  }

  public function getAreasSortedByDistance($areaId) {
    $name = $this->areaNames[$areaId];
    $areaCoordinates = $this->areas[$name];
    $this->findAllDistance($areaCoordinates);
    $this->sortByDistance();
    return $this->areas;
  }

  private function findAllDistance($areaCoordinates) {
    foreach ($this->areas as $key => &$value) {
      $value['dist'] = $this->calculateDistance($areaCoordinates['lat'], 
            $areaCoordinates['long'],
            $value['lat'],
            $value['long']);
    }
  }

  private function sortByDistance() {
    uasort($this->areas, function($a, $b) {
      return $a['dist'] <=> $b['dist'];
    });
  }

  private function calculateDistance($latitude1, $longitude1, $latitude2, $longitude2) {
    $lat1 = $latitude1 * M_PI / 180;
    $lat2 = $latitude2 * M_PI / 180;
    $long1 = $longitude1 * M_PI / 180;
    $long2 = $longitude2 * M_PI / 180;
 
    $cl1 = cos($lat1);
    $cl2 = cos($lat2);
    $sl1 = sin($lat1);
    $sl2 = sin($lat2);
    $delta = $long2 - $long1;
    $cdelta = cos($delta);
    $sdelta = sin($delta);
 
    $y = sqrt(pow($cl2 * $sdelta, 2) + pow($cl1 * $sl2 - $sl1 * $cl2 * $cdelta, 2));
    $x = $sl1 * $sl2 + $cl1 * $cl2 * $cdelta;
 
    $ad = atan2($y, $x);
    $dist = $ad * self::EARTH_RADIUS;
 
    return $dist / 1000;
  }
}