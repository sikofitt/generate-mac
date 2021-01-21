# sikofitt/generate-mac

### Small library to generate unique private mac addresses

[![pipeline status](https://repos.bgemi.net/sikofitt/generate-mac/badges/1.x/pipeline.svg)](https://repos.bgemi.net/sikofitt/generate-mac/-/commits/1.x)
[![coverage report](https://repos.bgemi.net/sikofitt/generate-mac/badges/1.x/coverage.svg)](https://repos.bgemi.net/sikofitt/generate-mac/-/commits/1.x)

#### Install

Requires PHP v8.0,  For versions compatible with PHP >= 7.3 use the 0.x branch.

[composer](https://getcomposer.org)
```bash
composer require sikofitt/generate-mac
```

#### Usage
```php
use Sikofitt\GenerateMac\Mac;

$mac = new Mac(); // default separator is ':'
// or
$mac->setSeparator(Mac::SEPARATOR_COLON);
$address = $mac->getAddress(); // ab:cd:ef:01:23:45

$mac = new Mac(Mac::SEPARATOR_DASH);
// or
$mac->setSeparator(Mac::SEPARATOR_DASH);
$address = $mac->getAddress(); // ab-cd-ef-01-23-45

$mac = new Mac(Mac::SEPARATOR_NONE);
// or
$mac->setSeparator(Mac::SEPARATOR_NONE);
$address = $mac->getAddress(); // abcdef012345
```

If you don't care that it is unique you can remove the check for private mac prefixes.

```php
$mac = new Mac(Mac::SEPARATOR_COLON, false);
// or
$mac->setUnique(false);

$address = $mac->getAddress();

// '52:54:00:ab:cd:ef',  QEMU virtual NIC prefix 52:54:00
// It's really not likely there will be a collision though.
```

Generate multiple mac addresses
```php
$addresses = $mac->getAddresses(10);

var_dump($addresses);
/*
 *   array (
 *       0 => '8a:20:0b:b7:c4:62',
 *       1 => '56:7d:47:56:e8:bd',
 *       2 => '2a:ae:7b:44:6f:9d',
 *       3 => '6a:36:1a:7c:04:3a',
 *       4 => '3a:3d:93:f5:a6:12',
 *       5 => '8a:85:ce:11:2c:a2',
 *       6 => '06:54:6f:b1:11:48',
 *       7 => 'c6:fe:9d:86:38:dd',
 *       8 => 'ba:39:b3:a2:a1:fa',
 *       9 => '32:73:c0:b3:62:27',
 *   );
 */

// if you call this with 1 as the count it will still
// return an array [0 => '32:73:c0:b3:62:27']
```

#### Using the console component

The console script requires [symfony/console](https://symfony.com/doc/current/components/console.html "Symfony Console Component")
```bash
user@localhost:~/generate-mac$ bin/generate-mac --count (int) --output (json|plain|string) --separator (none|colon|dash)
```
  * --count Generate {count} mac addresses
  * --output Output in selected format
    * string: (default) Outputs the mac address(es) with formatting
    * plain:  Outputs the mac address(es) with no formatting
    * json:   Output the mac address(es) in json format
  * --separator Outputs with the selected operator
    * colon: ':' (default)
    * dash:  '-'
    * none:  ''

_See bin/generate-mac --help_


#### Test

```bash
user@localhost:~/generate-mac$ vendor/bin/phpunit
```

#### License

[GPL-3.0](LICENSE)
