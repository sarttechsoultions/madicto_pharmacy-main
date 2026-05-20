<?php

namespace App\Http\Controllers;

use App\Models\CartModel;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function addCart(Request $request)
    {
        foreach ($request->medicines as $item) {

            $check = CartModel::where('coustmer_id', $request->coustmer_id)
                ->where('medicine_id', $item['medicine_id'])
                ->first();

            if ($check) {

                $check->quantity += $item['quantity'];
                $check->save();
            } else {

                CartModel::create([

                    'coustmer_id' => $request->coustmer_id,
                    'medicine_id' => $item['medicine_id'],
                    'quantity' => $item['quantity'],

                ]);
            }
        }

        return response()->json([
            'message' => 'Medicines Added Successfully'
        ]);
    }
}
