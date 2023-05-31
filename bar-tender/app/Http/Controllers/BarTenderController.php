<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class BarTenderController extends Controller
{

    // Move these to a class/config maybe - to have more flexibility
    private $drinkQueue = []; // Track the drink orders
    private $maxBeers = 2; // Maximum number of beers the barman can prepare at once
    private $maxDrinks = 1; // Maximum number of drinks the barman can prepare at once
    private $drinkPreparationTime = 5; // Default drink preparation time in seconds

    public function orderDrink(Request $request)
    {
        $customerNumber = $request->input('customer_number');
        $drinkType = $request->input('drink_type');

        if ($this->isOrderAccepted($drinkType)) {
            $this->serveDrink($customerNumber, $drinkType);
            return response('Drink will be served', Response::HTTP_OK);
        } else {
            return response('Order not accepted at the moment', Response::HTTP_TOO_MANY_REQUESTS);
        }
    }

    public function listOrders()
    {
        $drinkQueue = Cache::get('drink_queue', []);

        return response()->json($drinkQueue);
    }

    private function isOrderAccepted($drinkType)
    {
        $drinkQueue = Cache::get('drink_queue', []);

        if ($drinkType === 'BEER') {
            $beerCount = count(array_filter($drinkQueue, function ($order) {
                return $order === 'BEER';
            }));

            return $beerCount < $this->maxBeers;
        } elseif ($drinkType === 'DRINK') {
            $drinkCount = count(array_filter($drinkQueue, function ($order) {
                return $order === 'DRINK';
            }));

            return $drinkCount < $this->maxDrinks;
        }

        return false;
    }

    private function serveDrink($customerNumber, $drinkType)
    {
        $drinkQueue = Cache::get('drink_queue', []);
        array_push($drinkQueue, $drinkType);
        Cache::put('drink_queue', $drinkQueue);

        // Simulate drink preparation time
        sleep($this->drinkPreparationTime);
    }
}
