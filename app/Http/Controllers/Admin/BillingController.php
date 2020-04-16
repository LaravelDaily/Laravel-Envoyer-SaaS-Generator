<?php

namespace App\Http\Controllers\Admin;

use App\Country;
use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Payment;
use App\Role;
use Illuminate\Http\Request;
use Stripe\Coupon;
use Stripe\SetupIntent;
use Stripe\Stripe;

class BillingController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    public function index()
    {
        $plans  = Role::whereNotNull('stripe_plan_id')->get();
        $intent = auth()->user()->createSetupIntent();

        $currentPlan = auth()->user()->subscription('default') ?? null;
        $currentRole = ($currentPlan) ? Role::where('stripe_plan_id', $currentPlan->stripe_plan)->first() : null;

        $paymentMethods       = null;
        $defaultPaymentMethod = null;

        if (!is_null($currentPlan)) {
            $paymentMethods       = auth()->user()->paymentMethods();
            $defaultPaymentMethod = auth()->user()->defaultPaymentMethod();
        }

        $payments = Payment::where('user_id', auth()->id())->latest()->get();

        $countries = Country::orderBy('priority', 'desc')
            ->orderBy('id', 'asc')
            ->get();
        $prioritizedCountries = $countries->where('priority', '>', 0)->count();

        if ($prioritizedCountries > 0) {
            $countries->splice($prioritizedCountries, 0, "-------------------------");
        }

        return view('admin.billing.index', compact('plans', 'intent', 'currentRole', 'currentPlan',
            'paymentMethods', 'defaultPaymentMethod', 'payments', 'countries'));
    }

    public function checkout(CheckoutRequest $request)
    {
        $plan          = Role::find($request->input('checkout_plan_id'));
        $paymentMethod = $request->input('payment_method');
        $user          = $request->user();
        $price         = $plan->price;

        try {
            $currentPlan = $user->subscription('default') ?? null;
            $discount    = $request->input('discount_id') ? Coupon::retrieve($request->input('discount_id')) : null;

            auth()->user()->update($request->only([
                'billing_name', 'address_1', 'address_2', 'country_id', 'city', 'postcode',
            ]));

            if ($currentPlan) {
                $user->subscription('default')->withCoupon(optional($discount)->id)->swap($plan->stripe_plan_id);
            } else {
                if ($discount) {
                    if ($discount->percent_off) {
                        $price *= (100 - $discount->percent_off) / 100;
                    } else {
                        $price -= $price <= $discount->amount_off ? $price : $discount->amount_off;
                    }

                    $price = floor($price);
                }

                auth()->user()->newSubscription('default', $plan->stripe_plan_id)->withCoupon(optional($discount)->id)->create($paymentMethod);
                Payment::create([
                    'user_id'         => auth()->id(),
                    'plan_id'         => $plan->id,
                    'paid_amount'     => $price,
                    'original_amount' => $plan->price,
                    'discount_id'     => optional($discount)->id,
                ]);
            }

            $user->roles()->sync([$plan->id]);
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors([$ex->getMessage()]);
        }

        return redirect()->route('admin.billing.index')->withMessage(trans('global.billing.plan_purchased_successfully'));
    }

    public function cancel()
    {
        try {
            auth()->user()->subscription('default')->cancel();

        } catch (\Exception $ex) {
            return redirect()->back()->withErrors([$ex->getMessage()]);
        }

        return redirect()->route('admin.billing.index');
    }

    public function resume()
    {
        try {
            auth()->user()->subscription('default')->resume();

        } catch (\Exception $ex) {
            return redirect()->back()->withErrors([$ex->getMessage()]);
        }

        return redirect()->route('admin.billing.index');
    }

    public function checkDiscount(Request $request)
    {
        try {
            if (!$request->input('discount_code')) {
                throw new \Exception('Enter a code', 1);
            }

            $discount = Coupon::retrieve($request->input('discount_code'));

            if (!$discount->valid) {
                throw new \Exception('Code has expired', 1);
            }

        } catch (\Exception $ex) {
            return response(['message' => $ex->getMessage()], 400);
        }

        return response($discount);
    }

}
