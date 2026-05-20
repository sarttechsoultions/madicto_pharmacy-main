<?php

namespace App\Http\Controllers;

use App\Models\CartModel;
use App\Models\CoustmerMedicineModel as cstmodel;
use App\Models\medicineModel;
use Illuminate\Http\Request;

class CoustmerMedicineController extends Controller
{
    public function index()
    {
        $cstmedcine = cstmodel::with('coustmer', 'medicine')->get();
        $medicine = medicineModel::all();
        $cartItems = CartModel::all();
        return view('admin.coustmer-medicine', compact('cstmedcine', 'medicine', 'cartItems'));
    }
}
