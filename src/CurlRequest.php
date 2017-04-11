<?hh
/**
 * A file in the HackPostcodes library
 *
 * @author Matt Barber<mfmbarber@gmail.com>
**/
namespace mfmbarber\HackPostcodes;

class CurlRequest implements CurlInterface
{
  const string USER_AGENT = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36';

  private ?resource $curl;
  private array $options = [];

  /**
   * Upon instantiation set the URL for the curl request / create resource
   *
   * @param string $url The url for the curl request / endpoint
  **/
  public function __construct() {
    $this->setOption(CURLOPT_USERAGENT, self::USER_AGENT);
  }

  /**
   * Set the URL for the request, this will override the curl resource
   *
   * @param string $url
   *
   * @return void
  **/
  public function setUrl(string $url) : void {
    $this->curl = curl_init($url);
  }

  /**
   * Getter for the curl private attribute
   *
   * @return ?resource
  **/
  public function getCurl() : ?resource {
    return $this->curl;
  }

  /**
   * Set a curl option, this will be retained between requests and only
   * overriden by either setting over the top, removing or clearing the options
   *
   * @param int   $option The option to set  (usually as CURL constant)
   * @param mixed $value  The value can be a string, int or boolean
   *
   * @return void
  **/
  public function setOption(int $option, mixed $value) : void {
    $this->options[$option] = $value;
  }

  /**
   * Remove an option from the array of options
   *
   * @param int $option The option to remove (usually a CURL constant)
   *
   * @return void
  **/
  public function removeOption(int $option) : void {
    if (isset($this->options[$option])) {
      unset($this->options[$option]);
    }
  }

  /**
   * Clear all the options set in the options attribute
   *
   * @return void
  **/
  public function clearOptions() : void {
    $this->options = [];
  }

  /**
   * Return the options as an array
   *
   * @return array
  **/
  public function getOptions() : array {
    return $this->options;
  }

  /**
   * Get the status code from the reponse as an int
   *
   * @return int
  **/
  public function getStatusCode() : int {
    return curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

  }

  /**
   * Get the last request time
   *
   * @return float
  **/
  public function getRequestTime() : float {
    return curl_getinfo($this->curl, CURLINFO_TOTAL_TIME);
  }

  /**
   * Get the last curl error
   *
   * @return ?string
  **/
  public function getError() : ?string {
    $error = curl_error($this->curl);
    if (!(bool) curl_error($this->curl)) {
      return null;
    }
    return $error;
  }

  /**
   * Execute an async request using curl
   *
   * @return ?Awaitable<string>
  **/
  public async function execute() : ?Awaitable<string> {
    if (!curl_setopt_array($this->curl, $this->options)) {
      throw new \Exception("Couldn't set curl options ". implode(',', $this->options));
    }
    $result = await \HH\Asio\curl_exec($this->curl);
    if (!(bool) curl_error($this->curl)) {
      return $result;
    }
    throw new \Exception("Curl Error: {$this->getError()}");
  }

  /**
   * Close an active curl connection
  **/
  public function close() : void {
    if ($this->curl) {
      curl_close($this->curl);
      $this->curl = null;
    }
  }

}
