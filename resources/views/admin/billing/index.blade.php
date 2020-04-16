@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">
        {{ trans('global.billing.menu') }}
    </div>

    <div class="card-body">
        {{ trans('global.billing.current_plan') }}: <b>{{ $currentRole->title ?? trans('global.billing.trial_user') }}</b>
        <br />
        <hr />
        <div class="row">
            @foreach($plans as $plan)
                <div class="col">
                    <h2>{{ $plan->title }}</h2>
                    <h4>{{ config('saas.currency', '$') }}{{ number_format($plan->price / 100, 2) }} / {{ trans('global.billing.month') }}</h4>
                    @if($currentPlan && $plan->stripe_plan_id == $currentPlan->stripe_plan)
                        Your current plan.
                        @if(!$currentPlan->onGracePeriod())
                            <br /><br />
                            <a href="{{ route('admin.billing.cancel') }}" class="btn btn-danger" onclick="return confirm('Are you sure?')">Cancel plan</a>
                        @else
                            <br />
                            Your subscription will end on {{ $currentPlan->ends_at->toDateString() }}
                            <br /><br />
                            <a href="{{ route('admin.billing.resume') }}" class="btn btn-primary">Resume subscription</a>
                        @endif
                    @else
                        <a href="#" class="btn btn-primary button-plan" data-plan-id="{{ $plan->id }}" data-plan-price="{{ config('saas.currency', '$') }}{{ number_format($plan->price / 100, 2) }}" data-plan-title="{{ $plan->title }}">{{ trans('global.billing.choose_this_plan') }}</a>
                    @endif
                </div>
            @endforeach
        </div>

        <div id="checkout-block" class="d-none">
            <hr />
            <h3>Checkout</h3>
            <form action="{{ route('admin.billing.checkout') }}" method="POST" id="checkout-form">
                @csrf
                <input type="hidden" name="checkout_plan_id" id="checkout_plan_id" />
                <input type="hidden" name="payment_method" id="payment_method" value="" />
                <input type="hidden" name="discount_id" id="discount_id" value="" />

                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="billing_name">Billing Name</label>
                        <input type="text" class="form-control" name="billing_name" id="billing_name" required />
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="address_1">Address line 1</label>
                        <input type="text" class="form-control" name="address_1" id="address_1" required />
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="address_2">Address line 2 (optional)</label>
                        <input type="text" class="form-control" name="address_2" id="address_2" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="country_id">Country</label>
                        <select class="form-control" name="country_id" id="country_id" required>
                            @foreach($countries as $country)
                                @if(gettype($country) === "string")
                                    <option disabled>{{ $country }}</option>
                                @else
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="city">City</label>
                        <input type="text" class="form-control" name="city" id="city" required />
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="postcode">Postcode</label>
                        <input type="text" class="form-control" name="postcode" id="postcode" required />
                    </div>
                </div>
                <hr />
                <label for="discount_code">Discount code</label>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <input type="text" class="form-control" id="discount_code" />
                    </div>
                    <div class="col form-group">
                        <button type="button" class="btn btn-primary" id="apply-discount">Apply code</button>
                    </div>
                </div>
                <p id="discount-status"></p>
                <hr />
                <div id="checkout_plan_title" class="font-weight-bold"></div>
                <div class="row">
                    <div class="col-md-4">

                        <input id="card-holder-name" type="text" placeholder="Card holder name" class="form-control">

                        <!-- Stripe Elements Placeholder -->
                        <div id="card-element"></div>

                        <br />
                        <button id="card-button" class="btn btn-primary">
                            Pay
                        </button>

                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@if(!is_null($currentPlan))
    <br />
    <div class="card">
        <div class="card-header">Payment Methods</div>

        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Brand</th>
                        <th>Expires at</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($paymentMethods as $paymentMethod)
                        <tr>
                            <td>{{ $paymentMethod->card->brand }}</td>
                            <td>{{ $paymentMethod->card->exp_month }} / {{ $paymentMethod->card->exp_year }}</td>
                            <td>
                                @if($defaultPaymentMethod->id == $paymentMethod->id)
                                    default
                                @else
                                    <a href="{{ route('admin.payment_methods.default', $paymentMethod->id) }}">Mark as Default</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <br />
            <a href="{{ route('admin.payment_methods.create') }}" class="btn btn-primary">Add Payment Method</a>
        </div>
    </div>
@endif

<br />
<div class="card">
    <div class="card-header">{{ trans('global.payments.title') }}</div>

    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>{{ trans('global.payments.payment_date') }}</th>
                    <th>{{ trans('global.payments.amount') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                    <tr>
                        <td>{{ $payment->created_at }}</td>
                        <td>$ {{ number_format($payment->paid_amount / 100, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    $(document).ready(function() {
        let stripe = Stripe(_stripe_key)

        let elements = stripe.elements()
        let style = {
          base: {
            color: '#32325d',
            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            '::placeholder': {
              color: '#aab7c4'
            }
          },
          invalid: {
            color: '#fa755a',
            iconColor: '#fa755a'
          }
        }

        let card = elements.create('card', {style: style})
        card.mount('#card-element')
        let paymentMethod = null
        $('#checkout-form').on('submit', function (e) {
          $('#card-button').prop('disabled',true);
          if (paymentMethod) {
            return true
          }
          stripe.confirmCardSetup(
            "{{ $intent->client_secret }}",
            {
              payment_method: {
                card: card,
                billing_details: {name: $('#card-holder-name').val()}
              }
            }
          ).then(function (result) {
            if (result.error) {
              console.log(result)
              alert('error')
            } else {
              paymentMethod = result.setupIntent.payment_method
              $('#payment_method').val(paymentMethod)
              $('#checkout-form').submit()
            }
          })
          return false
        })

        $('.button-plan').on('click', function (e) {
          e.preventDefault();
          $('#checkout-block').removeClass('d-none');
          $('#checkout_plan_id').val($(this).data('plan-id'));
          $('#checkout_plan_title').text($(this).data('plan-title'));
          $('#card-button').text('Pay ' + $(this).data('plan-price'));
          $('#card-button').data('plan-price', $(this).data('plan-price').substr(1));
          $('#discount_code,#discount_id').val('');
          $('#discount-status').text('');
        })

        $('#apply-discount').on('click', function (e) {
          var $status     = $('p#discount-status');
          var $cardButton = $('#card-button');
          var price = parseFloat($cardButton.data('plan-price'));

          $status.text('');
          $(this).attr('disabled', true);
          e.preventDefault();
          $.post('{{ route('admin.billing.checkDiscount') }}', {
              _token: '{{ csrf_token() }}',
              discount_code: $('#discount_code').val()
          })
            .done(function (data) {

              if (data.percent_off) {
                price *= (100 - data.percent_off) / 100;
              } else {
                price -= price <= data.amount_off / 100 ? price : data.amount_off / 100;
              }
              price = Math.trunc(price * 100) / 100;
              $('#discount_id').val(data.id);
              $status.text('Discount applied');
            })
            .fail(function ({responseJSON}) {
              $('#discount_id').val('');
              $status.text(responseJSON.message);
            })
            .always(() => {
              $cardButton.text('Pay {{ config('saas.currency', '$') }}' + price.toFixed(2));
              $(this).attr('disabled', false);
            });
        })
      });
</script>
@endsection

@section('styles')
<style>
    .StripeElement {
        box-sizing: border-box;
        height: 40px;
        padding: 10px 12px;
        border: 1px solid transparent;
        border-radius: 4px;
        background-color: white;
        box-shadow: 0 1px 3px 0 #e6ebf1;
        -webkit-transition: box-shadow 150ms ease;
        transition: box-shadow 150ms ease;
    }

    .StripeElement--focus {
        box-shadow: 0 1px 3px 0 #cfd7df;
    }

    .StripeElement--invalid {
        border-color: #fa755a;
    }

    .StripeElement--webkit-autofill {
        background-color: #fefde5 !important;
    }
</style>
@endsection