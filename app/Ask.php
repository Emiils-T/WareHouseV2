<?php

namespace App;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;

class Ask
{
    public static function displayAsk(): void
    {
        $options = ["Create new user", "Access existing user inventory"];
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