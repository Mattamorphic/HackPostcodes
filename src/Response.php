<?hh //strict
/**
 * A file in the HackPostcodes library
 *
 * @author Matt Barber<mfmbarber@gmail.com>
**/
namespace mfmbarber\HackPostcodes;
/**
 * A response object for dealing with converted json data
 *
 * @category Class
 *
 * @author Matt Barber <mfmbarber@gmail.com>
**/
class Response {

  /**
   * Upon instantiation use constructor parameter promotion to create a data property
   *
   * @param array<arraykey, mixed> $data The data in the response (an assoc array)
  **/
  public function __construct(private array<arraykey, mixed> $data) {  }

  /**
   * Get a string from the response object given the array key
   *
   * @param arraykey $key The key in the response object
   *
   * @return string
  **/
  public function getString(arraykey $key) : string {
    $v = $this->getValue($key);
    invariant(is_string($v), 'key in data is not a string');
    return $v;
  }

  /**
   * Get a sub array as a sub response object
   *
   * @param arraykey $key The key in the response object
   *
   * @return Response
  **/
  public function getSubResponse(arraykey $key) : Response {
    $v = $this->getValue($key);
    invariant(is_array($v), 'key in data is not a string');
    return new Response($v);
  }

  /**
   * Get a number from an array given the array key
   *
   * @param arraykey $key The key in the response object
   *
   * @return num
  **/
  public function getNumber(arraykey $key) : num {
    $v = $this->getValue($key);
    invariant(is_numeric($v), 'key in data is not a string');
    return (float) $v;
  }

  /**
   * Get a boolean from an array given the array key
   *
   * @param arraykey $key The key in the response object
   *
   * @return bool
  **/
  public function getBoolean(arraykey $key) : bool {
    $v = $this->getValue($key);
    invariant(is_bool($v), 'key in data is not a string');
    return $v;
  }

  /**
   * Override a key value pair in the response object
   *
   * @param arraykey $key   The key in the response object
   * @param mixed    $value The value to override the key with
   *
   * @return void
  **/
  public function setValue(arraykey $key, mixed $value) : void {
    $this->data[$key] = $value;
  }

  /**
   * A generator allows us to loop over the items in the response
   *
   * @return Iterator<Response>
  **/
  public function loop() : Iterator<Response> {
    foreach ($this->data as $_ => $value) {
      if (!is_array($value)) {
        continue;
      }
      yield new Response($value);
    }
  }

  /**
   * Return the data in the response converted to a Map
   *
   * @return Map<arraykey, mixed>
  **/
  public function toMap() : Map<arraykey, mixed> {
    return new Map($this->data);
  }

  /**
   * Return the data in the response converted to a Vector (ditch keys)
   *
   * @return Vector<mixed>
  **/
  public function toVector() : Vector<mixed> {
    return new Vector(array_values($this->data));
  }

  /**
   * Private function, check to see if array_key_exists in the data
   *
   * @param arraykey $key The key in the array to look for 
  **/
  private function getValue(arraykey $key) : mixed {
    invariant(array_key_exists($key, $this->data), "Key not found in response");
    return $this->data[$key];
  }

}
