<?php

namespace App;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

class Warehouse
{
    private array $item;
    private string $baseDir;
    private string $selectedUser;


    public function __construct(string $baseDirectory, string $selectedUser)
    {
        $this->baseDir = $baseDirectory;
        $this->selectedUser = $selectedUser;
        $this->item = $this->getItems();
    }

    public function addItemToWarehouse(Product $product): void
    {
        $this->item[] = $product;
        $this->saveItemToFile();
    }

    public function saveItemToFile(): void
    {
        $filePath = $this->baseDir . "/data/" . $this->selectedUser . "_data.json";
        $jsonData = json_encode($this->item, JSON_PRETTY_PRINT);
        file_put_contents($filePath, $jsonData);
    }

    public function getItems(): array
    {
        $filePath = $this->baseDir . "/data/" . $this->selectedUser . "_data.json";
        if (!file_exists($filePath)) {
            return [];
        }
        $jsonData = file_get_contents($filePath);
        $data = json_decode($jsonData, true);

        $items = [];

        foreach ($data as $item) {
            if (!Uuid::isValid($item['id']) || Uuid::fromString($item['id'])->getVersion() != 4) {
                $item['id'] = Uuid::uuid4()->toString();
            }
            $items[] = new Product(
                $item['id'],
                $item['name'],
                new Carbon($item['dateOfCreation']),
                isset($item['lastUpdate']) ? new Carbon($item['lastUpdate']) : null,
                $item['units'],
                isset($item['expirationDate']) ? new Carbon($item['expirationDate']) : null,
                $item['price'],
            );
        }
        return $items;
    }

    public function removeItem(int $index): void
    {
        if (isset($this->item[$index])) {
            unset($this->item[$index]);
            $this->item = array_values($this->item);
            $this->saveItemToFile();
        } else {
            echo "ERROR: Invalid input\n";
        }
    }

    public function addUnits(int $index, int $amount): void
    {
        $items = $this->getItems();
        if (isset($items[$index])) {
            $items[$index]->addUnits($amount);

            $this->item = $items;
            $this->saveItemToFile();
        } else {
            echo "ERROR: Invalid input\n";
        }
    }

    public function updateItem($index, $date): void
    {
        $items = $this->getItems();
        if (isset($items[$index])) {
            $items[$index]->update($date);
            $this->item = $items;
            $this->saveItemToFile();
        } else {
            echo "ERROR: Invalid input\n";
        }
    }

    public function subtract(int $index, int $amount): void
    {
        $items = $this->getItems();
        if (isset($items[$index])) {
            $items[$index]->withdrawUnits($amount);
            $this->item = $items;
            $this->saveItemToFile();
        }
    }

    public function checkValue(int $index, int $amount): bool
    {
        $items = $this->getItems();
        if (!isset($items[$index]) && $amount > $items[$index]->getValue() || $amount === 0) {
            return false;
        }
        return true;
    }

    public function getCount(): int
    {
        $count = 0;
        foreach ($this->item as $item) {
            $count += $item->getUnits();
        }
        return $count;
    }

    public function getTotalValue(): int
    {
        $total = 0;
        foreach ($this->item as $item) {
            $total += $item->getUnits() * $item->getPrice();
        }
        return $total;
    }

    public function displayReport(): void
    {
        $rows = [];
        $rows[] = [
            $this->getCount(),
            $this->getTotalValue()];


        $output = new ConsoleOutput();
        $table = new Table($output);
        $table
            ->setHeaders([
                "Total unit count",
                "Total value",
            ])
            ->setRows($rows);
        $table->render();
    }


    public function displayItems(): void
    {
        $rows = [];
        foreach ($this->item as $index => $item) {

            $rows[] = [
                $index,
                $item->getId(),
                $item->getName(),
                $item->getUnits(),
                $item->getPrice(),
                $item->getDateOfCreation(),
                $item->getExpirationDate(),
                $item->getLastUpdate(),
            ];
        }
        $output = new ConsoleOutput();
        $table = new Table($output);
        $table
            ->setHeaders(["Index",
                "ID",
                "Name",
                "Units",
                "Price",
                "Date",
                "Expiration",
                "Last Update"])
            ->setRows($rows);
        $table->render();
    }

    public function displayItemsForEdit(): void
    {
        $rows = [];
        foreach ($this->item as $index => $item) {
            $nameCell = $item->getName();
            $unitsCell = $item->getUnits();

            $rows[] = [
                $index,
                $nameCell,
                $unitsCell
            ];
        }
        $output = new ConsoleOutput();
        $table = new Table($output);
        $table
            ->setHeaders(["Index", "Name", "Units"])
            ->setRows($rows);
        $table->render();

    }

    public function displayOptions(): void
    {
        $options = [
            "Add new Item.",
            "Show Items in Warehouse.",
            "Add more units",
            "Withdraw units",
            "Delete Item",
            "Show activity log",
            "Get report",
            "Exit"
        ];
        $rows = [];
        foreach ($options as $index => $option) {
            $rows[] = [
                $index + 1,
                $option
            ];
        }
        $output = new ConsoleOutput();
        $table = new Table($output);
        $table
            ->setHeaders(["Index", "Option"])
            ->setRows($rows);
        $table->render();
    }
}