<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\BarTenderService;

class BarTenderController extends Controller
{
    private $barTenderService;

    public function __construct(BarTenderService $barTenderService)
    {
        $this->barTenderService = $barTenderService;
    }

    public function orderDrink(Request $request)
    {
        $customerNumber = $request->input('customer_number');
        $drinkType = $request->input('drink_type');

        if ($this->barTenderService->isOrderAccepted($drinkType)) {
            $this->barTenderService->serveDrink($customerNumber, $drinkType);
            return response('Drink will be served', Response::HTTP_OK);
        } else {
            return response('Order not accepted at the moment', Response::HTTP_TOO_MANY_REQUESTS);
        }
    }

    public function listOrders()
    {
        return response()->json($this->barTenderService->getDrinkQueue());
    }
}
