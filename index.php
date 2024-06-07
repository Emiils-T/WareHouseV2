<?php

require_once './vendor/autoload.php';

use App\Product;
use App\Warehouse;
use App\Activity;
use App\Log;
use App\Customer;
use App\UserList;
use App\Ask;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

$baseDIR = __DIR__;
Ask::displayAsk();

$input = (int)readline("Enter selection: \n");

switch ($input) {
    case 1:
        $customerName = (string)readline("Customer name: ");
        $accessCode = (string)readline("Enter personal access Code: ");

        $customer = new Customer($customerName, $accessCode);
        $userList = new UserList($baseDIR);
        $userList->addToUserList($customer);
        $selectedUser = $customer;
        break;
    case 2:
        $userList = new UserList($baseDIR);
        $userList->displayUsers();

        $index = (int)readline("Enter index to select user: ");

        $customerList = $userList->getUserList();
        $inputAccessCode = readline("Enter access code: ");
        $selectedUser = null;
        if ($inputAccessCode === $customerList[$index]->getCode()) {
            $selectedUser = $customerList[$index];
            break;

        } else {
            echo "ERROR: Access codes don't match\n";
            exit;
        }
    default:
        echo "ERROR: Invalid input: $input\n";
        exit;
}

$warehouse = new Warehouse($baseDIR, $selectedUser->getName());

while (true) {
    $warehouse = new Warehouse($baseDIR, $selectedUser->getName());
    $warehouse->displayOptions();
    $choice = (int)readline("Enter index to select choice: ");

    switch ($choice) {
        case 1:
            $id = Uuid::uuid4();
            $id = $id->toString();
            $name = (string)readline("Enter product name: \n");
            if ($name == "") {
                echo "ERROR: Product name can't be empty\n";
                break;
            }
            $units = (int)readline("Enter amount of units: \n");
            if ($units === 0) {
                echo "ERROR: Enter valid amount of units\n";
                break;
            }

            $price = readline("Enter product of price or press n to continue: \n");

            if ($price === ""|| $price === "n") {
                $price = null;
            }else{
                $price = (int)$price; ;
            }
            $expirationDate = readline("Enter expiration date YYYY-MM-DD or press n to continue: \n");
            if($expirationDate === ""|| $expirationDate === "n"){
                $expirationDate=null;
            }else{
                $expirationDate = Carbon::createFromFormat("Y-m-d",$expirationDate);
            }


            $dateOfCreation = Carbon::now('Europe/Riga');

            $lastUpdate = null;
            $product = new Product(
                $id,
                $name,
                $dateOfCreation,
                $lastUpdate,
                $units,
                $expirationDate,
                $price);


            $warehouse = new Warehouse($baseDIR, $selectedUser->getName());
            $warehouse->addItemToWarehouse($product);

            $activity = new Activity("{$selectedUser->getName()}:New item input: $name",$dateOfCreation,);
            $log = new Log($baseDIR);
            $log->addActivityToLog($activity);
            /*var_dump($dateOfCreation);*/

            break;
        case 2:
            $warehouse = new Warehouse($baseDIR, $selectedUser->getName());
            $warehouse->displayItems();
            $dateOfCreation = carbon::now('Europe/Riga');

            $activity = new Activity("{$selectedUser->getName()}:View items in warehouse", $dateOfCreation);
            $log = new Log($baseDIR);
            $log->addActivityToLog($activity);
            break;

        case 3:
            $warehouse = new Warehouse($baseDIR, $selectedUser->getName());
            $warehouse->displayItemsForEdit();

            $itemIndex = (int)readline("Select item to add units to: \n");
            $amount = (int)readline("Enter amount of units: \n");

            if (!$warehouse->checkValue($itemIndex, $amount)) {
                echo "ERROR: Invalid input: \n";
                break;
            }

            $dateOfCreation = carbon::now();
            $warehouse->updateItem($itemIndex, $dateOfCreation);
            $warehouse->addUnits($itemIndex, $amount);

            $selectedItem = $warehouse->getItems()[$itemIndex]->getName();
            $activity = new Activity("{$selectedUser->getName()}:Added $amount $selectedItem", $dateOfCreation);
            $log = new Log($baseDIR);
            $log->addActivityToLog($activity);
            break;

        case 4:
            $warehouse = new Warehouse($baseDIR, $selectedUser->getName());
            $warehouse->displayItemsForEdit();

            $itemIndex = (int)readline("Select item to withdraw from: \n");
            $amount = (int)readline("Enter amount of units: \n");

            $dateOfCreation = Carbon::now();
            if (!$warehouse->checkValue($itemIndex, $amount)) {
                echo "ERROR: Invalid input: \n";
                break;
            }
            $warehouse->subtract($itemIndex, $amount);
            $warehouse->updateItem($itemIndex, $dateOfCreation);

            $selectedItem = $warehouse->getItems()[$itemIndex]->getName();
            $activity = new Activity("{$selectedUser->getName()}:Withdrew $amount $selectedItem", carbon::now());
            $log = new Log($baseDIR);
            $log->addActivityToLog($activity);
            break;

        case 5:
            $warehouse = new Warehouse($baseDIR, $selectedUser->getName());
            $warehouse->displayItemsForEdit();

            echo "Enter index to delete item or n to return to selection: ";
            $deleteIndex = readline();
            if ($deleteIndex=== "" || $deleteIndex === "n" ) {
                break;
            }
            $deleteIndex = (int)$deleteIndex;
            $selectedItem = $warehouse->getItems()[$deleteIndex]->getName();
            $warehouse->removeItem($deleteIndex);

            $dateOfCreation = Carbon::now();

            $activity = new Activity("{$selectedUser->getName()}:Deleted $selectedItem from inventory", $dateOfCreation);
            $log = new Log($baseDIR);
            $log->addActivityToLog($activity);
            break;

        case 6:
            $log = new Log($baseDIR);
            $log->displayLog();
            break;
        case 7:
            $warehouse= new Warehouse($baseDIR, $selectedUser->getName());
            $warehouse->displayReport();
        case 8:
            exit;
        default:
            echo "Enter valid input\n";
            break;
    }
}