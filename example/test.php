<?hh

include 'vendor/autoload.php';
use mfmbarber\HackPostcodes\Postcodes;
use mfmbarber\HackPostcodes\Curl\CurlRequest;

function main() : void {
  $postcodes = new Postcodes(new CurlRequest());

  $x = $postcodes->lookup('DT51HG');
  var_dump($x);
  sleep(1);

  $y = $postcodes->lookupBulk(Vector {'DT51HG', 'BH122BL'});
  var_dump($y);
  sleep(1);
  
  $z = $postcodes->getDistance('DT51HG', 'BH122BL');
  var_dump($z);
  sleep(1);
}

main();
