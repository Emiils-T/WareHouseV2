<?php

namespace App;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;

class UserList
{
    private string $baseDirectory;
    private array $customer;

    public function __construct(string $baseDirectory)
    {
        $this->baseDirectory = $baseDirectory;
        $this->customer = $this->getUserList();
    }

    public function addToUserList(Customer $customer): void
    {
        $this->customer[] = $customer;
        $this->saveToList();
    }

    public function saveToList(): void
    {
        $filePath = $this->baseDirectory . "/userList/userList.json";
        $jsonData = json_encode($this->customer, JSON_PRETTY_PRINT);
        file_put_contents($filePath, $jsonData);
    }

    public function getUserList(): array
    {
        $filePath = $this->baseDirectory . "/userList/userList.json";
        if (!file_exists($filePath)) {
            return [];
        }
        $jsonData = file_get_contents($filePath);
        $data = json_decode($jsonData, true);

        $users = [];

        foreach ($data as $user) {
            $users[] = new Customer(
                $user['name'],
                $user['code'],
            );
        }
        return $users;
    }

    public function displayUsers(): void
    {
        $rows = [];
        foreach ($this->customer as $index => $user) {
            $nameCell = $user->getName();

            $rows[] = [
                $index,
                $nameCell,
            ];
        }
        $output = new ConsoleOutput();
        $table = new Table($output);

        $table
            ->setHeaders(["Index", "User"])
            ->setRows($rows);
        $table->render();
    }
}