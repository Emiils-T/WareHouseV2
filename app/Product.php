<?php

namespace App;

use Carbon\Carbon;
use JsonSerializable;

class Product implements JsonSerializable
{
    private string $id;
    private string $name;
    private Carbon $dateOfCreation;
    private ?Carbon $lastUpdate;
    private int $units;
    private ?Carbon $expirationDate;
    private ?int $price;

    public function __construct(string  $id,
                                string  $name,
                                Carbon  $dateOfCreation,
                                ?Carbon $lastUpdate,
                                int     $units,
                                ?Carbon $expirationDate = null,
                                ?int    $price = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->dateOfCreation = $dateOfCreation;
        $this->lastUpdate = $lastUpdate;
        $this->units = $units;
        $this->expirationDate = $expirationDate;
        $this->price = $price;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDateOfCreation(): Carbon
    {
        return $this->dateOfCreation;
    }

    public function getLastUpdate(): ?Carbon
    {
        return $this->lastUpdate;
    }

    public function getUnits(): int
    {
        return $this->units;
    }

    public function getExpirationDate(): ?Carbon
    {
        return $this->expirationDate;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function addUnits(int $units): void
    {
        $this->units += $units;
    }

    public function withdrawUnits(int $units): void
    {
        $this->units = $this->units - $units;
    }

    public function update(Carbon $date): void
    {
        $this->lastUpdate = $date;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'dateOfCreation' => $this->dateOfCreation ? $this->dateOfCreation->toIso8601String() : null,
            'lastUpdate' => $this->lastUpdate ? $this->lastUpdate->toIso8601String() : null,
            'units' => $this->units,
            'expirationDate' => $this->expirationDate ? $this->expirationDate->toIso8601String() : null,
            'price' => $this->price,
        ];
    }
}
