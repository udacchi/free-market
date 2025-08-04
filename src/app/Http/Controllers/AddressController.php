<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddressRequest;
use App\Models\Address;

class AddressController extends Controller
{
    public function store(AddressRequest $request)
    {
        $validated = $request->validated();

        Address::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'postal' => $validated['postal'],
            'address' => $validated['address'],
            'building' => $validated['building'],
        ]);

        return redirect()->back()->with('success', '住所を登録しました。');
    }
}
