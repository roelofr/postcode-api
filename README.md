# PostcodeAPI.nu Laravel plugin

Easy access the [postcodeapi.nu](https://postcodeapi.nu) using a simple-to-use,
well tested Laravel plugin.

## License

[Licensed under MIT](LICENSE.md).

## Requirements

- PHP 7.4+
- Laravel 5.1 (Supports Statamic)

## Installation

1.  Firstly, pull the addon off Packagist
    ```
    composer require roelofr/postcodeapi
    ```
2.  (Laravel <5.5) Add Service Provider to `config/app.php`
    ```php
    'providers' => [
        // …
        Roelofr\PostcodeApi\ServiceProvider::class
    ]
    ```
4.  Publish the configuration
    ```
    php artian vendor:publish --provider=Roelofr\PostcodeApi\ServiceProvider
    ```
5.  You're good to go!

## Usage

This plugin provides a contract (`PostcodeApiContract`) and a facade
(`PostcodeApi`), use whichever you want.

The main route is the `retrieve` method, which expects a postcode and a number.
```php
public function retrieve(string $postcode, string $number): AddressInformation;
```

The method will automatically try to clean the postcode and number to allow for
API submission. After retireval an AddressInformation model is returned, or a
`NotFoundException` is thrown.

### Exceptions

- A `MalformedDataException`, if we cannot find a Dutch post code and house
  number in the paramters.
- A `AuthenticationFailureException` if your API key does not work.
- A `NotFoundException` if the given postcode + number cannot be found.

### Without Laravel

To get this plugin to work without Laravel is fairly tricky, as it requires a
configuration and a cache according to Laravel's schematics.

If you want to rewrite parts of the code to work without Laravel, feel free to
open a PR. It's somewhat out of scope though.
