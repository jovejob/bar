<?php

namespace App\Services\Implementations;

use App\Services\Contracts\DrinkStorageInterface;
use Illuminate\Support\Facades\Cache;

class CacheDrinkStorage implements DrinkStorageInterface
{
    private $cacheKey = 'drink_queue';

    public function getDrinkQueue()
    {
        return Cache::get($this->cacheKey, []);
    }

    public function setDrinkQueue($drinkQueue)
    {
        Cache::put($this->cacheKey, $drinkQueue);
    }
}
