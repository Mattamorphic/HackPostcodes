<?hh 
/**
 * A file in the HackPostcodes library
 *
 * @author Matt Barber<mfmbarber@gmail.com>
**/
namespace mfmbarber\HackPostcodes\Tests\Unit;

use PHPUnit\Framework\TestCase;

use mfmbarber\HackPostcodes\Response;

class TestResponse extends TestCase {

  /**
   * Test standard array returns correct value given key
   *
   * @return void
  **/
  public function testSingleLayerArrayGetKey() : void {
    $r = new Response(str_split('test'));
    $this->assertInternalType(\PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $r->getString(0));
    $this->assertEquals('t', $r->getString(0));
  }

  /**
   * Test getting a sub response from a response
   *
   * @return void
  **/
  public function testMultiLayerArrayGetKey() : void {
    $arr = [
      'name' => 'matt',
      'colours' => [
          'a' => 'red',
          'b' => 'green',
          'c' => 'blue'
      ]
    ];
    $r = new Response($arr);
    $this->assertInstanceOf(Response::class, $r->getSubResponse('colours'));
    $this->assertEquals('red', $r->getSubResponse('colours')->getString('a'));
  }

  /**
   * Test getting a typed value from a response
   *
   * @return void
  **/
  public function testGetNumber() : void {
    $r = new Response(['a' => 1]);
    $this->assertEquals(1, $r->getNumber('a'));
    $this->assertInternalType(\PHPUnit_Framework_Constraint_IsType::TYPE_INT, $r->getNumber('a'));
  }

  /**
   * Test the loop functionality for if the response is composed of sub responses
   *
   * @return void
  **/
  public function testGeneratorLoop() : void {
    $expected = ['d', 'a'];
    $r = new Response(
      [
        ['a', 'b', 'c'],
        ['d', 'e', 'f']
      ]
    );
    foreach ($r->loop() as $inner_r) {
      $this->assertEquals(array_pop($expected), $inner_r->getString(0));
    }
  }

  /**
   * Test conversion to a vector
   *
   * @return void
  **/
  public function testToVector() : void {
    $r = new Response(['a' => 0]);
    $v = $r->toVector();
    $this->assertInstanceOf(Vector::class, $v);
    $this->assertEquals(0, $v->at(0));
  }

  /**
   * Test conversion to a map
   *
   * @return void
  **/
  public function testToMap() : void {
    $r = new Response(['b' => 0]);
    $m = $r->toMap();
    $this->assertInstanceOf(Map::class, $m);
    $this->assertEquals(0, $m->at('b'));
  }


}
