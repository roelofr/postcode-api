<?php

declare(strict_types=1);

namespace Tests\Unit;

use InvalidArgumentException;
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

    /**
     * Tests from-array parsing
     * @param array $data
     * @return void
     * @dataProvider provideArrayConstructors
     */
    public function testFromArray(bool $pass, array $data): void
    {
        // Fail-case
        if (!$pass) {
            $this->expectException(InvalidArgumentException::class);
        }

        // Get instance
        $instance = AddressInformation::fromArray($data);

        // Check instance
        $this->assertEquals($data, $instance->toArray());
        $this->assertEquals(json_encode($data), json_encode($instance));
    }

    /**
     * Tests string conversion
     * @return void
     */
    public function testToString()
    {
        $data = ['1234AB', 77, 'Dorpsstraat', 'Amsterdam', 'Amsterdam', 'Noord-Holland'];
        $instance = new AddressInformation(...$data);

        $this->assertSame(
            "Dorpsstraat 77,\n1234AB Amsterdam (Noord-Holland)", (string) $instance
        );
    }

    public function provideConstructors(): array
    {
        return [
            ['1234AB', 77, 'Dorpsstraat', 'Amsterdam', 'Amsterdam', 'Noord-Holland', true],
            ['', 0, '', '', '', '', false],
        ];
    }

    public function provideArrayConstructors(): array
    {
        return array_map(function ($values) {
            $pass = array_pop($values);
            $data = array_combine(
                ['postcode', 'number', 'street', 'city', 'municipality', 'province'],
                $values
            );
            return [$pass, $data];
        }, $this->provideConstructors());
    }
}
