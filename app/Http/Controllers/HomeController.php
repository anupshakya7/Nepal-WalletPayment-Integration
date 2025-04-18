<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(){
        $products = Product::where('status','available')->limit(6)->get();
        
        return view('frontend.home',compact('products'));
    }
}
