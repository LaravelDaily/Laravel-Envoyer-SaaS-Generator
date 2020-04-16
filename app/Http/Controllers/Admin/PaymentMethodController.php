<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function create()
    {
        $intent = auth()->user()->createSetupIntent();

        return view('admin.billing.paymentMethod', compact('intent'));
    }

    public function store(Request $request)
    {
        try {
            auth()->user()->addPaymentMethod($request->input('new_payment_method'));

            if ($request->input('default') == 1) {
                auth()->user()->updateDefaultPaymentMethod($request->input('new_payment_method'));
            }

        } catch (\Exception $e) {
            return redirect()->back()->withError($e->getMessage());
        }

        return redirect()->route('admin.billing.index')->withMessage('Payment method added successfully');
    }

    public function markDefault(Request $request, $paymentMethod)
    {
        try {
            auth()->user()->updateDefaultPaymentMethod($paymentMethod);
        } catch (\Exception $e) {
            return redirect()->back()->withError($e->getMessage());
        }

        return redirect()->route('admin.billing.index')->withMessage('Payment method updated successfully');
    }

}
