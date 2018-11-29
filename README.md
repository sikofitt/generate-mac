## Small library to generate unique private mac addresses

### Usage
```php
use Sikofitt\GenerateMac\Mac;

$mac = new Mac(); // default is ':'
$address = $mac->getAddress(); // ab:cd:ef:01:23:45

$mac = new Mac('-');
$address = $mac->getAddress(); // ab-cd-ef-01-23-45

$mac = new Mac('');
$address = $mac->getAddress(); // abcdef012345
```

If you don't care that it is unique you can remove the check for private mac prefixes.

```php
    $mac = new Mac(':', false);
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
```