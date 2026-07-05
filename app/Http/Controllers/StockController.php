<?php

namespace App\Http\Controllers;

use App\Models\UniformStock;

class StockController extends Controller
{
    /**
     * Display a public stock detail page.
     */
    public function show(UniformStock $stock)
    {
        if (! $stock->is_visible) {
            abort(404);
        }

        return view('stocks.show', compact('stock'));
    }
}
