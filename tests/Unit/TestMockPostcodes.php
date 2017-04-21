<?hh
/**
 * A file in the HackPostcodes library
 *
 * @author Matt Barber<mfmbarber@gmail.com>
**/
namespace mfmbarber\HackPostcodes\Tests\Unit;

use PHPUnit\Framework\TestCase;

use mfmbarber\HackPostcodes\Postcodes;
use mfmbarber\HackPostcodes\Curl\CurlRequest;
use mfmbarber\HackPostcodes\Curl\CurlInterface;
use mfmbarber\HackPostcodes\Response;

/**
 * Test suite for the Postcodes class
 *
 * @category Tests
 *
 * @author Matt Barber <mfmbarber@gmail.com>
**/
class TestMockPostcodes extends TestCase
{
  /**
   * Test the lookup method for the postcodes class
   *
   * @return void
  **/
  public function testLookup() : void {
    // some data removed for clarity
    $data = '{
      "status": 200,
      "result": {
          "country": "England"
      }
    }';
    // create a new postcodes object with our mock CR
    $postcodes = new Postcodes(new MockCr($data));
    $result = $postcodes->lookup('bh12 2bl');
    // check instance and equals
    $this->assertInstanceOf(Map::class, $result);
    $this->assertEquals($result->at('country'), 'England');
  }

/**
 * Tests that invalid post codes are caught
 *
 * @expectedException        \Exception
 * @expectedExceptionMessage !5G%TG is not a valid postcode
 *
 * @return void
**/
  public function testLookupParseThrowInvalidException() : void {
    $postcodes = new Postcodes(new MockCr(''));
    $postcodes->lookup('!5G %tg');
  }

  /**
   * Tests that lookupBulk returns the expected structure and values
   *
   * @return void
  **/
  public function testLookupBulk() : void {
    $data = '
    {
      "status": 200,
      "result":
      [
        {
          "query": "OX49 5NU",
          "result": {
            "region" : "South East"
          }
        },
        {
          "query": "M32 0JG",
          "result": {
            "region" : "North West"
          }
        }
      ]
    }';
    $postcodes = new Postcodes(new MockCr($data));
    $result = $postcodes->lookupBulk(
      Vector {
        "OX49 5NU",
        "M32 0JG"
      }
    );
    $this->assertInstanceOf(Vector::class, $result);
    $this->assertInstanceOf(Map::class, $result->at(0));
    $this->assertEquals('South East', $result->at(0)->at('region'));
  }

  /**
   * Tests looking up a set of locations based on longitude / latitude
   *
   * @return void
  **/
  public function testLookupLatLon() : void {
    $data = '{
      "status" : 200,
      "result" : [
        {
          "postcode" : "BH12 2BL"
        },
        {
          "postcode": "BH12 2BW"
        }
      ]
    }';
    $postcodes = new Postcodes(new MockCr($data));
    $result = $postcodes->lookupLatLon(Pair {-1.93115910963689, 50.7299678681388});
    $this->assertInstanceOf(Vector::class, $result);
    $this->assertEquals(2, $result->count());
    $this->assertEquals('BH12 2BL', $result->at(0)->at('postcode'));
  }

  /**
   * Tests looking up a bulk set of longitude and latitudes
   *
   * @return void
  **/
  public function testLookupBulkLatLon() : void {
    $data = '
    {
      "status": 200,
      "result":
      [
        {
          "query": {
            "longitude": "0.629834723775309",
            "latitude": "51.7923246977375"
          },
          "result":
            [
              {
                "postcode": "CM8 1EF"
              },
              {
                "postcode": "CM8 1EU"
              },
              {
                "postcode": "CM8 1PH"
              },
              {
                "postcode": "CM8 1PQ"
              }
            ]
          },
          {
            "query": {
              "longitude": "-2.49690382054704",
              "latitude": "53.5351312861402"
            },
            "result":
              [
                {
                  "postcode": "M46 9WU"
                },
                {
                  "postcode": "M46 9XF"
                },
                {
                  "postcode": "M46 9XE"
                },
                {
                  "postcode": "M46 9NX"
                },
                {
                  "postcode": "M46 9NU"
                }
            ]
          }
        ]
      }';
    $postcodes = new Postcodes(new MockCr($data));
    $result = $postcodes->lookupBulkLatLong(
      Vector {
        Pair {
          0.629834723775309,
          51.7923246977375
        },
        Pair {
          -2.49690382054704,
          53.5351312861402
        }
      }
    );
    $this->assertInstanceOf(Vector::class, $result);
    $this->assertInstanceOf(Vector::class, $result->at(0));
    $this->assertInstanceOf(Map::class, $result->at(0)->at(0));
    $this->assertEquals('CM8 1EF', $result->at(0)->at(0)->at('postcode'));
  }

  /**
   * Tests the is valid method against a valid address
   *
   * @return void
  **/
  public function testIsValid() : void {
    $data = '{
      "status" : 200,
      "result" : true
    }';
    $postcodes = new Postcodes(new MockCr($data));
    $this->assertTrue($postcodes->isValid('bh12 2bl'));
  }

  /**
   * Tests the is valid method against an invalid address
   *
   * @return void
  **/
  public function testIsNotValid() : void {
    $data = '{
      "status" : 200,
      "result" : false
    }';
    $postcodes = new Postcodes(new MockCr($data));
    $this->assertFalse($postcodes->isValid('DT5111'));
  }

  /**
   * Tests the autocompletion of incomplete post codes
   *
   * @return void
  **/
  public function testAutocomplete() : void {
    $data = '{
      "status": 200,
      "result": [
        "BH12 1AA",
        "BH12 1AB",
        "BH12 1AD",
        "BH12 1AE",
        "BH12 1AH",
        "BH12 1AJ",
        "BH12 1AL",
        "BH12 1AN",
        "BH12 1AP",
        "BH12 1AR"
      ]
    }';
    $postcodes = new Postcodes(new MockCr($data));
    $result = $postcodes->autocomplete('BH12');
    $this->assertInstanceOf(Vector::class, $result);
    $this->assertEquals(10, $result->count());
    $this->assertEquals('BH12 1AA', $result->at(0));
  }

  /**
   * Tests getting the distance between two long / lat points on a spherical plain
   *
   * @return void
  **/
  public function testGetDistance() : void {
    $data = '{
      "status": 200,
      "result": [
        {
          "result" : {
            "longitude": -1.93115910963689,
            "latitude": 50.7299678681388
          }
        }, {
          "result" : {
            "longitude": -2.42684168122331,
            "latitude": 50.647813093741
          }
        }
      ]
    }';
    $mockCr = new MockCr($data);
    $postcodes = new Postcodes($mockCr);
    $this->assertEquals(36, (int) $postcodes->getDistance('BH12 2BL', 'DT3 6NZ'));
  }

}
