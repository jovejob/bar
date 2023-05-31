<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class BarTenderController extends Controller
{
    private $maxBeers = 2;
    private $maxDrinks = 1;
    private $drinkPreparationTime = 5;

    public function orderDrink(Request $request)
    {
        $customerNumber = $request->input('customer_number');
        $drinkType = $request->input('drink_type');
        $orderIdentifier = $request->header('Order-Identifier');

        // Check if the order has already been served
        if ($this->isOrderServed($orderIdentifier)) {
            return response('Order has already been served', Response::HTTP_OK);
        }

        if ($this->isOrderAccepted($drinkType)) {
            $this->serveDrink($customerNumber, $drinkType, $orderIdentifier);
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
        }

        if ($drinkType === 'DRINK') {
            $drinkCount = count(array_filter($drinkQueue, function ($order) {
                return $order === 'DRINK';
            }));

            return $drinkCount < $this->maxDrinks;
        }

        return false;
    }

    private function serveDrink($customerNumber, $drinkType, $orderIdentifier)
    {
        $drinkQueue = Cache::get('drink_queue', []);
        array_push($drinkQueue, $drinkType);
        Cache::put('drink_queue', $drinkQueue);

        // Mark the order as served using the order identifier
        Cache::put('served_orders:' . $orderIdentifier, true);

        // Simulate drink preparation time
        sleep($this->drinkPreparationTime);
    }

    private function isOrderServed($orderIdentifier)
    {
        return Cache::has('served_orders:' . $orderIdentifier);
    }
}
