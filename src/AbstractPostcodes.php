<?hh
/**
 * A file in the HackPostcodes library
 *
 * @author Matt Barber<mfmbarber@gmail.com>
**/
namespace mfmbarber\HackPostcodes;
/**
 * Abstract implementation of the HackPostcodes primary functions
 *
 * @category AbstractClass
 *
 * @author Matt Barber <mfmbarber@gmail.com>
**/
class AbstractPostcodes implements PostcodesInterface {

  // API constants
  const string API_URL = 'https://api.postcodes.io';
  const string POSTCODES_END = '/postcodes';
  const string OUTCODES_END = '/outcodes';
  const string POSTCODES_REGEX = '/^[a-zA-Z0-9]{6,8}$/';
  const int BULK_LIMIT = 100;

  // Our curl object
  private CurlInterface $cr;

  /**
   * Upon instantiation set our CurlRequest objkect
  **/
  public function __construct(CurlInterface $cr) {
    $this->cr = $cr;
    $this->cr->setOption(
      CURLOPT_HTTPHEADER,
      ['Accept: application/json','Content-Type: application/json']
    );
  }

  /**
   * Build our Postcode specific request
   *
   * @param string             $endpoint The endpoint for the request
   * @param Map<string, mixed> $params   The params for the request
   * @param bool               $post     Is this a POST HTTP request
   *
   * @return Awaitable<object>
  **/
  public async function request(string $endpoint, Map<string, mixed> $params, bool $post) : Awaitable<Map> {
    // If GET then build http params
    if (!$post) {
      $endpoint.= '?' . http_build_query($params->toArray());
    }
    // If POST build other params on CURL object
    if ($post) {
      $this->cr->setOption(CURLOPT_POST, true);
      $this->cr->setOption(CURLOPT_RETURNTRANSFER, true);
      $this->cr->setOption(CURLOPT_POSTFIELDS, json_encode($params->toArray()));
    }
    // Set the URL
    $this->cr->setUrl(self::API_URL . $endpoint);
    // Fire!
    $result = await $this->cr->execute();
    $result = json_decode($result, true);

    // if we don't have a 200 - throw exception
    if ((int) $result['status'] !== 200) {
      throw new \Exception($result['status'] . ' : ' . $result['error']);
    }

    // if the result is not an array - make it one
    if (!is_array($result['result'])) {
      $result['result'] = ['result' => $result['result']];
    }
    return new Map($result['result']);
  }

  /**
   * Protected : convert the postcode into an API friendly string, and check it's
   * in a valid format
   *
   * @param string $postcode The postcode to parse
   *
   * @return string
  **/
  protected function parse(string $postcode) : string {
    // TODO : Make this infer postcode or lat long and validate accordingly
    $postcode = str_replace(' ', '', strtoupper($postcode));
    if (!$this->isValidFormat($postcode)) {
      throw new \Exception("$postcode is not a valid postcode");
    }
    return $postcode;
  }

  /**
   * Protected : Check the format of the postcode against the regex constant
   *
   * @param string $postcode The postcode to check
   *
   * @return bool
  **/
  protected function isValidFormat(string $postcode) : bool {
    // TODO : Make this infer postcode or lat long and validate accordingly
    return (bool) (preg_match(self::POSTCODES_REGEX, $postcode) > 0);
  }

}
