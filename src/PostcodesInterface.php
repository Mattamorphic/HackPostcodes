<?hh
/**
 * This file is part of the HackPostcodes library
 *
 * @author Matt Barber <mfmbarber@gmail.com>
 * @category class
**/
namespace mfmbarber\HackPostcodes;
/**
 * An interface for any Postcode interfaces we might implement
 *
 * @author Matt Barber <mfmbarber@gmail.com>
 * @category Interface
**/
interface PostcodesInterface {
  public function request(string $endpoint, Map<string, mixed> $params, bool $post);
}
