<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class PriceUpdateController extends Controller
{
    public function priceshow()
    {
        $shop = Auth::user();

        // Execute the GraphQL query to fetch products
        $productsResponse = $shop->api()->graph('
        {
            products(first: 250) {
                edges {
                    node {
                        id
                        title
                    }
                }
            }
        }
        ');
        $products = $productsResponse['body']->container['data']['products']['edges'] ?? [];

        // Return the view with the data
        return view('welcome', compact('products'));
    }

    public function priceupdate(Request $request)
    {

        // Get the request data from the form
        $oldrate = $request->input('oldrate');
        $newrate = $request->input('newrate');

        $shop = Auth::user();

        // Utilize $response here if needed, for example, to check success or log errors
        return redirect(URL::tokenRoute('home'));
    }

}
