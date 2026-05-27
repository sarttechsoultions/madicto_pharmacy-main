<?php

namespace App\Http\Controllers;

use App\Models\ReviewModel;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = ReviewModel::all();
        return view('admin.reviews', compact('reviews'));
    }
}
