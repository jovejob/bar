<?php

namespace App\Services;

use App\Services\Contracts\DrinkStorageInterface;

class BarTenderService
{
    // todo move them further to a config file or DB or whatever mechanism we think its better
    private $drinkQueue = [];
    private $maxBeers = 2;
    private $maxDrinks = 1;
    private $drinkPreparationTime = 5;
    private $drinkStorage;

    public function __construct(DrinkStorageInterface $drinkStorage)
    {
        $this->drinkStorage = $drinkStorage;
        $this->drinkQueue = $this->drinkStorage->getDrinkQueue();
    }

    public function isOrderAccepted($drinkType)
    {
        // todo switch case for more options and flexibility
        if ($drinkType === 'BEER') {
            return $this->isBeerOrderAccepted();
        }

        if ($drinkType === 'DRINK') {
            return $this->isDrinkOrderAccepted();
        }

        return false;
    }

    private function isBeerOrderAccepted()
    {
        $beerCount = count(array_filter($this->drinkQueue, function ($order) {
            return $order === 'BEER';
        }));

        return $beerCount < $this->maxBeers;
    }

    private function isDrinkOrderAccepted()
    {
        $drinkCount = count(array_filter($this->drinkQueue, function ($order) {
            return $order === 'DRINK';
        }));

        return $drinkCount < $this->maxDrinks;
    }

    public function serveDrink($customerNumber, $drinkType)
    {
        array_push($this->drinkQueue, $drinkType);
        $this->drinkStorage->setDrinkQueue($this->drinkQueue);

        // Simulate drink preparation time
        sleep($this->drinkPreparationTime);
    }

    public function getDrinkQueue()
    {
        return $this->drinkQueue;
    }
}
