<?php

declare(strict_types=1);

namespace Ajay\TestGQ\Model;

use Ajay\TestGQ\Api\Data\StoreInterface;
use Ajay\TestGQ\Model\ResourceModel\Store as StoreResourceModel;
use Magento\Framework\Model\AbstractExtensibleModel;

class Store extends AbstractExtensibleModel implements StoreInterface
{

    protected function _construct()
    {
        $this->_init(StoreResourceModel::class);
    }

    public function getName(): ?string
    {
        return $this->getData(self::NAME);
    }

    public function setName(?string $name): void
    {
        $this->setData(self::NAME, $name);
    }

    public function getStreet(): ?string
    {
        return $this->getData(self::STREET);
    }

    public function setStreet(?string $street): void
    {
        $this->setData(self::STREET, $street);
    }

    public function getStreetNum(): ?int
    {
        return $this->getData(self::STREET_NUM);
    }

    public function setStreetNum(?int $streetNum): void
    {
        $this->setData(self::STREET_NUM, $streetNum);
    }

    public function getCity(): ?string
    {
        return $this->getData(self::CITY);
    }

    public function setCity(?string $city): void
    {
        $this->setData(self::CITY, $city);
    }

    public function getPostCode(): ?int
    {
        return $this->getData(self::POSTCODE);
    }

    public function setPostcode(?int $postCode): void
    {
        $this->setData(self::POSTCODE, $postCode);
    }

    public function getLatitude(): ?float
    {
        return $this->getData(self::LATITUDE);
    }

    public function setLatitude(?float $latitude): void
    {
        $this->setData(self::LATITUDE, $latitude);
    }

    public function getLongitude(): ?float
    {
        return $this->getData(self::LONGITUDE);
    }

    public function setLongitude(?float $longitude): void
    {
        $this->setData(self::LONGITUDE, $longitude);
    }
}