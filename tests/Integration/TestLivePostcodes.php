<?hh
/**
 * A file in the HackPostcodes library
 *
 * @author Matt Barber<mfmbarber@gmail.com>
**/
namespace mfmbarber\HackPostcodes\Tests\Integration;

use PHPUnit\Framework\TestCase;

use mfmbarber\HackPostcodes\Postcodes;
use mfmbarber\HackPostcodes\CurlRequest;
use mfmbarber\HackPostcodes\CurlInterface;

/**
 * Test suite for the Postcodes class
 *
 * @category Tests
 *
 * @author Matt Barber <mfmbarber@gmail.com>
**/
class TestLivePostcodes extends TestCase
{

  public function tearDown() : void {
    sleep(2); // Limit the requests to the API
  }
  /**
   * Test the lookup method for the postcodes class
   *
   * @return void
  **/
  public function testLookup() : void {
    $postcodes = new Postcodes(new CurlRequest());
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
    $postcodes = new Postcodes(new CurlRequest());
    $postcodes->lookup('!5G %tg');
  }

  /**
   * Tests that lookupBulk returns the expected structure and values
   *
   * @return void
  **/
  public function testLookupBulk() : void {
    $postcodes = new Postcodes(new CurlRequest());
    $result = $postcodes->lookupBulk(
      Vector {
        "OX49 5NU",
        "M32 0JG"
      }
    );
    $this->assertInstanceOf(Vector::class, $result);
    $this->assertInstanceOf(Map::class, $result->at(0));
    $this->assertContains($result->at(0)->at('region'), ['South East', 'North West']);
  }

  /**
   * Tests looking up a set of locations based on longitude / latitude
   *
   * @return void
  **/
  public function testLookupLatLon() : void {
    $postcodes = new Postcodes(new CurlRequest());
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
    $postcodes = new Postcodes(new CurlRequest());
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
    $this->assertContains(
      $result->at(0)->at(0)->at('postcode'),
      [
        'CM8 1EF',
        'CM8 1EU',
        'CM8 1PH',
        'CM8 1PQ',
        'M46 9WU',
        'M46 9XF',
        'M46 9XE',
        'M46 9NX',
        'M46 9NU',
        'M46 9QB'
      ]
    );
  }

  /**
   * Tests the is valid method against a valid address
   *
   * @return void
  **/
  public function testIsValid() : void {
    $postcodes = new Postcodes(new CurlRequest());
    $this->assertTrue($postcodes->isValid('bh12 2bl'));
  }

  /**
   * Tests the is valid method against an invalid address
   *
   * @return void
  **/
  public function testIsNotValid() : void {
    $postcodes = new Postcodes(new CurlRequest());
    $this->assertFalse($postcodes->isValid('DT5111'));
  }

  /**
   * Tests the autocompletion of incomplete post codes
   *
   * @return void
  **/
  public function testAutocomplete() : void {
    $postcodes = new Postcodes(new CurlRequest());
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
    $postcodes = new Postcodes(new CurlRequest());
    $this->assertEquals(36, (int) $postcodes->getDistance('BH12 2BL', 'DT3 6NZ'));
  }

}
