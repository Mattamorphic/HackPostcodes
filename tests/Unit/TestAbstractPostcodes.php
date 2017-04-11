<?hh
/**
 * A file in the HackPostcodes library
 *
 * @author Matt Barber<mfmbarber@gmail.com>
**/
namespace mfmbarber\HackPostcodes\Tests\Unit;

use PHPUnit\Framework\TestCase;


use mfmbarber\HackPostcodes\AbstractPostcodes;
use mfmbarber\HackPostcodes\CurlRequest;
use mfmbarber\HackPostcodes\CurlInterface;
/**
 * Test suite for the AbstractPostcodes class
 *
 * @category Tests
 *
 * @author Matt Barber <mfmbarber@gmail.com>
**/
class TestAbstractPostcodes extends TestCase
{
  /**
   * Mock a Curl Request / Abstract Postcodes class and test
   *
   * @return void
  **/
  public function testExecute() : void {
    $data = '{
      "status": 200,
      "result": {
          "postcode": "BH12 2BL",
          "quality": 1,
          "eastings": 404955,
          "northings": 92263,
          "country": "England",
          "nhs_ha": "South West",
          "longitude": -1.93115910963689,
          "latitude": 50.7299678681388,
          "parliamentary_constituency": "Poole",
          "european_electoral_region": "South West",
          "primary_care_trust": "Bournemouth and Poole Teaching",
          "region": "South West",
          "lsoa": "Poole 010A",
          "msoa": "Poole 010",
          "incode": "2BL",
          "outcode": "BH12",
          "admin_district": "Poole",
          "parish": "Poole, unparished area",
          "admin_county": null,
          "admin_ward": "Branksome West",
          "ccg": "NHS Dorset",
          "nuts": "Bournemouth and Poole",
          "codes": {
              "admin_district": "E06000029",
              "admin_county": "E99999999",
              "admin_ward": "E05010536",
              "parish": "E43000024",
              "ccg": "E38000045",
              "nuts": "UKK21"
          }
      }
    }';
    $ap = new MockAp(new MockCr($data));
    $result = \HH\Asio\join($ap->request($ap::POSTCODES_END . '/t35t', Map {}, false));
    $this->assertInstanceOf(Map::class, $result);
    $this->assertEquals($result->at('postcode'), 'BH12 2BL');
  }

  /**
   * Test the base exception handler
   *
   * @expectedException        \Exception
   * @expectedExceptionMessage 404 : page not found
  **/
  public function testException() : void {
    $data = '{
      "status" : "404",
      "error" : "page not found"
    }';
    $ap = new MockAp(new MockCr($data));
    \HH\Asio\join($ap->request($ap::POSTCODES_END, Map {}, false));
  }
}
