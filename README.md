# HackPostcodes #

HackPostcodes is a library written in Hack that wraps around the [https://postcodes.io](https://postcodes.io) API.

## Motivation ##
I've been learning the Hack programming language, and the Hip Hop Virtual Machine (HHVM) - so seemed like a good excuse to make something usable.

## Dependancies ##

- This was written using HHVM v3.18, it's probably backwards compatible to a degree though
- PHPUnit 5.7.* - There isn't compatibility with HHVM above this yet


## Installation ##

You can install this library using composer:
```
$ composer install mfmbarber/HackPostcodes
```

## Usage ##

To use the Postcodes library you first need to ensure you instantiate the CurlRequest class, and inject this into Postcodes as a constructor dependancy.
This allows you to swap out the CurlRequest class with your own implementation if required. It also allows us to mock our dependancy when testing.

Once you have instantiated the Postcodes class you can then call any of the following methods off of this:

- lookup(string $postcode) : Map
Use this to lookup a single postcode and get back a map of the result

- lookupBulk(Vector<string> $postcodes) : Vector<Map>
Use this to lookup a set of postcodes and get back a vector of maps

- lookupLatLon(Pair<float, float> $geolocation) : Vector<maps>
Use this to look up at latitude / longitude point and return a vector of maps - where each map represents a local postcodes to that position

- lookupBulkLatLong(Vector<Pair<float, float>> $geolocations) : Vector<Vector<Map>>
Use this to lookup a vector of longitude / latitude points and return a vector, of vectors, of maps. Where each first level represents a corresponding longitude / latitude point, and each sub-vector represents the local postcodes to that location

- isValid(string $postcode) : bool
Check to see if a postcode is valid (both regex and a UK postcode)

- getNearest(string $postcode) : Vector<Map>
Find the postcodes nearest to a given postcode, and return this as a Vector of Maps

- autocomplete(string $postcode, int $limit = 10) : Vector<string>
Given part of a postcode, and the amount of results to return (limit is 100), return a Vector of potential full postcode strings

- random() : Map
Return a random postcode as a map

-getDistance(string $postcode1, string $postcode2) : num
Return the shortest distance (straight line) between two postcodes, this uses the Haversine formula to calculate.


```
<?hh

include 'vendor/autoload.php';
use mfmbarber\HackPostcodes\Postcodes;
use mfmbarber\HackPostcodes\CurlRequest;

$postcodes = new Postcodes(new CurlRequest());

$x = $postcodes->lookup('DT51HG');
var_dump($x);

$y = $postcodes->lookupBulk(Vector {'DT51HG', 'BH122BL'});
var_dump($y);

$z = $postcodes->getDistance('DT51HG', 'BH122BL');
var_dump($z);
```

## Tests ##

Tests have been written using PHPUnit, and can be run from the root of the project:
```
$ hhvm vendor/bin/phpunit tests/
```

## Contributing ##

To contribute to this project

- Create a fork of the project
- Create a feature/bugfix branch off of develop
- Commit your changes
- Write your tests
- Once you are happy - PR back into develop
- Once reviewed, the PR is accepted.
- Hurrah!

Ensure you comment your code!

## License ##
This project is fully open sourced under MIT license
