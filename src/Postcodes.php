<?hh
/**
 * Main file for the HackPostcodes library
 *
 * @author Matt Barber <mfmbarber@gmail.com>
**/
namespace mfmbarber\HackPostcodes;
/**
 * Class for use with the Postcode.io API
 *
 * @category Class
 * @author Matt Barber <mfmbarber@gmail.com>
**/
final class Postcodes extends AbstractPostcodes
{
  /**
   * Lookup a post code in postcodes.io and return a Map
   *
   * @param string $postcode A postcode to lookup
   *
   * @return Map <string, mixed>
  **/
  public function lookup(string $postcode) : Map {
    $postcode = $this->parse($postcode);
    $result = \HH\Asio\join($this->request(self::POSTCODES_END ."/$postcode", Map {}, false));
    return new Map($result);
  }

  /**
   * Lookup a vector of postcodes from postcodes.io and return a vector of maps
   *
   * @param Vector<string> $postcodes A vector of postcodes
   *
   * @return Vector<Map>
  **/
  public function lookupBulk(Vector<string> $postcodes) : Vector<Map> {
    $postcodes = Map {
      'postcodes' => $postcodes->map($p ==> $this->parse($p))->toArray()
    };
    $result = \HH\Asio\join($this->request(self::POSTCODES_END, $postcodes, true));
    $vResult = new Vector($result);
    return $vResult->map($x ==> new Map($x['result']));
  }

  /**
   * Lookup a post code in postcodes.io based on lat/long and return Map
   *
   * @param Pair<float, float> $geolocation A lat/long pair
   *
   * @return Map
  **/
  public function lookupLatLon(Pair<float, float> $geolocation) : Vector<Map> {
    $result = \HH\Asio\join(
      $this->request(
        self::POSTCODES_END,
        Map {
          'lon' => $geolocation->at(0),
          'lat' => $geolocation->at(1)
        },
        false
      )
    );
    $vResult = new Vector($result);
    return $vResult->map($x ==> new Map($x));
  }

  /**
   * Lookup a vector of lat/long pairs from postcodes.io and return a vector or
   * maps
   *
   * @param Vector<Pair<float, float>> $geolocations The geolocations to check
   *
   * @return Vector<Map>
  **/
  public function lookupBulkLatLong(Vector<Pair<float, float>> $geolocations) : Vector<Vector<Map>> {
    $payload = $geolocations->map(
      $p ==> Map {
        'longitude' => $p->at(0),
        'latitude' => $p->at(1)
      }
    );
    $result = \HH\Asio\join(
      $this->request(
        self::POSTCODES_END,
        Map {'geolocations' => $payload},
        true
      )
    );
    return $result->map(
      function (array $geo) : Vector {
        $data = new Vector($geo['result']);
        return $data->map($x ==> new Map($x));
      }
    )->toVector();
  }

  /**
   * Is the given Postcode valid
   *
   * @param string $postcode The postcode to test
   *
   * @return bool
  **/
  public function isValid(string $postcode) : bool {
    $postcode = $this->parse($postcode);
    $result = \HH\Asio\join(
      $this->request(self::POSTCODES_END . "/$postcode/validate", Map {}, false)
    );
    return (bool) $result->at('result');
  }

  /**
   * Given a postcode, return those nearest to it
   *
   * @param string $postcode The postcode to checkdate
   *
   * @return Vector<Map>
  **/
  public function getNearest(string $postcode) : Vector<Map> {
    $postcode = $this->parse($postcode);
    $result = \HH\Asio\join(
      $this->request(self::POSTCODES_END . "/$postcode/nearest", Map {}, false)
    );
    $vResult = new Vector($result);
    return $vResult->map($x ==> new Map($x));
  }

  /**
   * Autocomple a postcode, offering a set of 0 to limit autocomplete options
   *
   * @param string $postcode A postcode to autocomplete
   * @param int    $limit    A limit of postcodes to complete
   *
   * @return Vector<string>
  **/
  public function autocomplete(string $postcode, int $limit = 10) : Vector<string> {
    if ($limit && ($limit < 0 || $limit > self::BULK_LIMIT)) {
      throw new \Exception('Limit cannot be less 0, or greater than 100');
    }
    $result = \HH\Asio\join(
      $this->request(
        self::POSTCODES_END . "/$postcode/autocomplete",
        Map {'limit' => $limit},
        false
      )
    );
    return new Vector($result);
  }

  /**
   * Return a random post code from the postcodes.io api
   *
   * @return Map
  **/
  public function random() : Map {
    $result = \HH\Asio\join(
      $this->request(
        '/random' . self::POSTCODES_END,
        Map {},
        false
      )
    );
    return new Map($result);
  }

  /**
   * Measure distance between postcodes using the Haversine formula
   * This is the shortest path on the surface of the earth (a spherical plane)
   *
   * @param string $postcode1 The first postcode
   * @param string $postcode2 The second postcode
   *
   * @return num
  **/
  public function getDistance(string $postcode1, string $postcode2) : num {
    // Radius of the earth in km
    $rad = 6371;
    // Get the information from postcodes.io
    list($p1, $p2) = $this->lookupBulk(Vector {$postcode1, $postcode2});
    $dLat = deg2rad($p2->at('latitude') - $p1->at('latitude'));
    $dLon = deg2rad($p2->at('longitude') - $p1->at('longitude'));

    $a = sin($dLat / 2) * sin($dLat / 2) +
         cos(deg2rad($p1->at('latitude'))) * cos(deg2rad($p2->at('latitude'))) *
         sin($dLon / 2) * sin($dLon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return $rad * $c;
  }
}
