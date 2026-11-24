<?php

namespace App\Http\Controllers;

use App\Models\ReviewModel;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = ReviewModel::with(['coustmer', 'medicine'])
            ->latest()
            ->paginate(15);
        return view('admin.reviews', compact('reviews'));
    }
}
