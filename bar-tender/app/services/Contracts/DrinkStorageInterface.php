<?php

namespace App\Services\Contracts;

interface DrinkStorageInterface
{
    public function getDrinkQueue();

    public function setDrinkQueue($drinkQueue);
}
