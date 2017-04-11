<?hh
/**
 * A file in the HackPostcodes library
 *
 * @author Matt Barber<mfmbarber@gmail.com>
**/
namespace mfmbarber\HackPostcodes\Tests\Unit;
use mfmbarber\HackPostcodes\CurlRequest;
/**
 * A Mock of the CurlRequest class - we have to use this as it contains async
 *
 * @category Tests
 *
 * @author Matt Barber <mfmbarber@gmail.com>
**/
class MockCr extends CurlRequest {

  // Vector to hold our data
  private Vector<string> $mockData;

  // Holds a reference to the current index
  private int $index = 0;

  /**
    * The mock must be constructed with at least one string of data
    *
    * @param string $data The data to add
    *
    * @return void
  **/
  public function __construct(string $data) {
    parent::__construct();
    $this->mockData = Vector {};
    $this->setData($data);
  }
  /**
   * Mocking execute
   * Will return the current item from mockData, and increment pointer
   *
   * @return Awaitable<string>
  **/
  public async function execute() : Awaitable<string> {
    // if empty, reset the index and return an empty string
    if ($this->mockData->isEmpty()) {
      $this->index = 0;
      return '';
    }
    // return the current data at index, and increment index
    $data = $this->mockData->at($this->index);
    ++$this->index;
    return $data;
  }

  /**
   * A helper method to allow us to add more data to the mock data object
   *
   * @param string $data The data to add to the mock
   *
   * @return void
  **/
  public function setData(string $data) : void  {
    $this->mockData->add($data);
  }


}
