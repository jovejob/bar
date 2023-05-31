<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class BarTenderService
{
    private $maxBeers = 2;
    private $maxDrinks = 1;
    private $drinkPreparationTime = 5;

    public function isOrderAccepted($drinkType)
    {
        $drinkQueue = $this->getDrinkQueue();

        if ($drinkType === 'BEER') {
            return $this->isBeerOrderAccepted($drinkQueue);
        }

        if ($drinkType === 'DRINK') {
            return $this->isDrinkOrderAccepted($drinkQueue);
        }

        return false;
    }

    public function serveDrink($customerNumber, $drinkType, $orderIdentifier)
    {
        $drinkQueue = $this->getDrinkQueue();
        array_push($drinkQueue, $drinkType);
        $this->setDrinkQueue($drinkQueue);

        // Mark the order as served using the order identifier
        $this->markOrderAsServed($orderIdentifier);

        // Simulate drink preparation time
        sleep($this->drinkPreparationTime);
    }

    public function isOrderServed($orderIdentifier)
    {
        return Cache::has('served_orders:' . $orderIdentifier);
    }

    public function getDrinkQueue()
    {
        return Cache::get('drink_queue', []);
    }

    public function setDrinkQueue($drinkQueue)
    {
        Cache::put('drink_queue', $drinkQueue);
    }

    private function isBeerOrderAccepted($drinkQueue)
    {
        $beerCount = count(array_filter($drinkQueue, function ($order) {
            return $order === 'BEER';
        }));

        return $beerCount < $this->maxBeers;
    }

    private function isDrinkOrderAccepted($drinkQueue)
    {
        $drinkCount = count(array_filter($drinkQueue, function ($order) {
            return $order === 'DRINK';
        }));

        return $drinkCount < $this->maxDrinks;
    }

    private function markOrderAsServed($orderIdentifier)
    {
        Cache::put('served_orders:' . $orderIdentifier, true);
    }
}
