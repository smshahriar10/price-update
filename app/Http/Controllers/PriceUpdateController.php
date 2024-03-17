<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

class PriceUpdateController extends Controller
{
    public function priceshow()
    {
        $shop = Auth::user();
    
        // GraphQL query to fetch both oldrate and newrate metafield values
        $response = $shop->api()->graph('
            {
              shop {
                oldRateMetafield: metafield(namespace: "priceupdate", key: "oldrate") {
                  value
                }
                newRateMetafield: metafield(namespace: "priceupdate", key: "newrate") {
                  value
                }
              }
            }
        ');
    
        // Extracting the values from the response
        $oldRate = isset($response['body']->container['data']['shop']['oldRateMetafield']['value']) ? $response['body']->container['data']['shop']['oldRateMetafield']['value'] : '';
        $newRate = isset($response['body']->container['data']['shop']['newRateMetafield']['value']) ? $response['body']->container['data']['shop']['newRateMetafield']['value'] : '';
    
        return view('welcome', compact('oldRate', 'newRate'));
    }    

    public function priceupdate(Request $request)
    {
        // Get the request data from the form
        $oldrate = $request->input('oldrate');
        $newrate = $request->input('newrate');
        $shop = Auth::user();

        $shopData = $shop->api()->rest('GET', '/admin/api/2024-01/shop.json');
        if (!$shopData['errors'] && isset($shopData['body']['shop']['id'])) {
            $shopId = $shopData['body']['shop']['id'];

            // Define the GraphQL mutation for updating the metafield directly within this method
            $mutation = '
                mutation MetafieldsSet($metafields: [MetafieldsSetInput!]!) {
                    metafieldsSet(metafields: $metafields) {
                        metafields {
                            key
                            namespace
                            value
                            createdAt
                            updatedAt
                        }
                        userErrors {
                            field
                            message
                            code
                        }
                    }
                }
            ';

            // Prepare the metafields data
            $metafieldsData = [
                "metafields" => [
                    [
                        "namespace" => "priceupdate",
                        "key" => "oldrate",
                        "value" => $oldrate,
                        "type" => "number_decimal",
                        "ownerId" => "gid://shopify/Shop/{$shopId}",
                    ],
                    [
                        "namespace" => "priceupdate",
                        "key" => "newrate",
                        "value" => $newrate,
                        "type" => "number_decimal",
                        "ownerId" => "gid://shopify/Shop/{$shopId}",
                    ]
                ]
            ];

            // Execute the GraphQL mutation
            $response = $shop->api()->graph($mutation, $metafieldsData);
        }

        // After successfully updating exchange rates:
        if (!$shopData['errors']) {

            // Fetch all products
            $productsData = $shop->api()->rest('GET', '/admin/api/2024-01/products.json');
            if (!$productsData['errors']) {
                $products = $productsData['body']['products'];

                foreach ($products as $product) {
                    foreach ($product['variants'] as $variant) {
                        // Reverse calculate the implied USD price without considering the .99 adjustment
                        $usdPriceImplied = $variant['price'] / $oldrate;

                        // Calculate the new BDT price using today's rate
                        $newPriceInBDT = $usdPriceImplied * $newrate;

                        // Instead of adding .99 here, consider rounding to the nearest whole number
                        // if you're aiming for a consistent base that can revert cleanly
                        $newPriceRounded = round($newPriceInBDT);

                        // Only add .99 for the final display price, not for storage or further calculations
                        $newPriceFinal = $newPriceRounded - 0.01; // Adjusting to ensure the final price ends in .99

                        // Update the product variant with the new price
                        $updateResponse = $shop->api()->rest('PUT', "/admin/api/2024-01/variants/{$variant['id']}.json", [
                            'variant' => [
                                'id' => $variant['id'],
                                'price' => $newPriceFinal
                            ]
                        ]);

                        if ($updateResponse['errors']) {
                            Log::error('Failed to update product variant price', [
                                'variantId' => $variant['id'],
                                'errors' => $updateResponse['errors']
                            ]);
                        }
                    }
                }
                return redirect(URL::tokenRoute('home'))->with('message', 'Product prices updated successfully.');
            } else {
                Log::error('Failed to fetch products', ['errors' => $productsData['errors']]);
                session()->flash('error', 'Failed to fetch products.');
                return redirect(URL::tokenRoute('home'))->withInput();
            }
        }
    }

}
