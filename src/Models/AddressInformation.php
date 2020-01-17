<?php

declare(strict_types=1);

namespace Roelofr\PostcodeApi\Models;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use JsonSerializable;

/**
 * Read-only model with address information
 *
 * @license MIT
 */
class AddressInformation implements JsonSerializable
{
    /**
     * Builds address information from array
     * @param array $data
     * @return Roelofr\PostcodeApi\Models\AddressInformation
     * @throws InvalidArgumentException
     */
    public static function fromArray(array $data): self
    {
        // Get data from JSON
        $fields = [
            Arr::get($data, 'postcode'),
            Arr::get($data, 'number'),
            Arr::get($data, 'street'),
            Arr::get($data, 'city'),
            Arr::get($data, 'municipality'),
            Arr::get($data, 'province'),
        ];

        // Validate all items are occupied
        if (!empty(array_filter($fields, fn ($value) => empty($value)))) {
            throw new InvalidArgumentException('JSON is missing fields');
        }

        // Return item
        return new self(...$fields);
    }

    private string $postcode;
    private int $number;
    private string $street;
    private string $city;
    private string $municipality;
    private string $province;

    /**
     * Creates a new lookup result
     *
     * @param string $postcode
     * @param int $number
     * @param string $street
     * @param string $city
     * @param string $municipality
     * @param string $province
     */
    public function __construct(
        string $postcode,
        int $number,
        string $street,
        string $city,
        string $municipality,
        string $province
    ) {
        $this->postcode = $postcode;
        $this->number = $number;
        $this->street = $street;
        $this->city = $city;
        $this->municipality = $municipality;
        $this->province = $province;
    }

    /**
     * @return string
     */
    public function getPostcode(): string
    {
        return $this->postcode;
    }

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getMunicipality(): string
    {
        return $this->municipality;
    }

    /**
     * @return string
     */
    public function getProvince(): string
    {
        return $this->province;
    }

    /**
     * Returns address as you would print it on an envelope.
     * Contains newlines
     * @return string
     */
    public function __toString(): string
    {
        return sprintf(
            "%s %s,\n%s %s (%s)",
            $this->street,
            $this->number,
            $this->postcode,
            $this->city,
            $this->province
        );
    }

    /**
     * Converts object to an array.
     * @return array
     */
    public function toArray(): array
    {
        return [
            'postcode' => $this->postcode,
            'number' => $this->number,
            'street' => $this->street,
            'city' => $this->city,
            'municipality' => $this->municipality,
            'province' => $this->province,
        ];
    }

    /**
     * Returns data for JSON output
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Convert object to serializable array
     * @return array
     * @see https://www.php.net/manual/en/migration74.new-features.php#migration74.new-features.standard.magic-serialize
     */
    public function __serialize(): array
    {
        return $this->toArray();
    }

    /**
     * Load data from array
     * @param array $data
     * @return void
     * @see https://www.php.net/manual/en/migration74.new-features.php#migration74.new-features.standard.magic-serialize
     */
    public function __unserialize(array $data): void
    {
        $this->postcode = $data['postcode'];
        $this->number = $data['number'];
        $this->street = $data['street'];
        $this->city = $data['city'];
        $this->municipality = $data['municipality'];
        $this->province = $data['province'];
    }
}
