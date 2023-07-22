<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Something;
use Carbon\Carbon;
use Illuminate\Http\Request;

class doSomethingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $selectedFromDate = $request->selectedFromDate ?? now()->subDays(31)->startOfDay()->toISOString();
        $selectedToDate = $request->selectedToDate ?? now()->startOfDay()->toISOString();

        $somethings = Something::whereBetween('created_at', [Carbon::create($request->selectedFromDate)->toISOString(), Carbon::create($request->selectedToDate)->toISOString()])->get();

        return [
            'selectedFromDate' => $selectedFromDate,
            'selectedToDate' => $selectedToDate,
            'somethings' => $somethings,
        ];
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
