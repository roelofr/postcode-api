<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\ExpectationFailedException;
use Roelofr\PostcodeApi\Models\AddressInformation;
use Tests\TestCase;

class AddressInformationTest extends TestCase
{
    /**
     * Tests the constructor and getters (should be 1:1)
     *
     * @param string $postcode
     * @param int $number
     * @param string $street
     * @param string $city
     * @param string $municipality
     * @param string $province
     * @return void
     * @dataProvider provideConstructors
     */
    public function testConstructor(
        string $postcode,
        int $number,
        string $street,
        string $city,
        string $municipality,
        string $province
    ): void {
        // Address information
        $instance = new AddressInformation($postcode, $number, $street, $city, $municipality, $province);

        // Validate properties
        $this->assertSame($postcode, $instance->getPostcode());
        $this->assertSame($number, $instance->getNumber());
        $this->assertSame($street, $instance->getStreet());
        $this->assertSame($city, $instance->getCity());
        $this->assertSame($municipality, $instance->getMunicipality());
        $this->assertSame($province, $instance->getProvince());
    }

    public function provideConstructors(): array
    {
        return [
            ['1234AB', 77, 'Dorpsstraat', 'Amsterdam', 'Amsterdam', 'Noord-Holland'],
            ['', 0, '', '', '', ''],
        ];
    }
}
