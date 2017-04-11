<?hh
/**
 * This file is part of the HackPostcodes library
 *
 * @author Matt Barber <mfmbarber@gmail.com>
 * @category Interface
**/
namespace mfmbarber\HackPostcodes;

/**
 * An interface for any curl classes we might implement
 *
 * @author Matt Barber <mfmbarber@gmail.com>
 * @category Interface
**/
interface CurlInterface {

  /**
   * Set a URL, and reset the curl object????
  **/
  public function setUrl(string $url) : void;

  /**
   * Set a curl option on the curl resource
   *
   * @param int    $option The option to set (use PHPs constants)
   * @param mixed  $value  The value to use for this option
   *
   * @return void
  **/
  public function setOption(int $option, mixed $value) : void;

  /**
   * Remove a curl option from the curl resource
   *
   * @param int $option The option to remove (use PHPs curl constants)
   *
   * @return void
  **/
  public function removeOption(int $option) : void;

  /**
   * Clear the curl options
   *
   * @return void
  **/
  public function clearOptions() : void;

  /**
   * Return the curl options
   *
   * @return array
  **/
  public function getOptions() : array;

  /**
   * Execute a curl request
   *
   * @return ?string
  **/
  //public function execute() : ?string;

  /**
   * Get the status code response from a curl request
   *
   * @return ?int
  **/
  public function getStatusCode() : int;

  /**
   * Get the error from a curl request
   *
   * @return ?string
  **/
  public function getError() : ?string;

  /**
   * Get the time taken for the request
   *
   * @return float
  **/
  public function getRequestTime() : float;

  /**
   * Close a curl connection
   *
   * @return void
  **/
  public function close() : void;

  /**
   * Execute the curl request with the given data
   *
  **/
  public function execute();
}
