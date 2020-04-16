@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">
        New Payment Method
    </div>

    <div class="card-body">
        <form action="{{ route('admin.payment_methods.store') }}" method="POST" id="new-payment-method-form">
            @csrf
            <input type="hidden" name="new_payment_method" id="new_payment_method" value="" />

            <div class="row">
                <div class="col-md-4">

                    <input id="new-card-holder-name" type="text" placeholder="Card holder name" class="form-control">

                    <!-- Stripe Elements Placeholder -->
                    <div id="new-card-element"></div>

                    <br />
                    <input type="checkbox" name="default" value="1" /> Mark as Default Method
                    <br /><br />

                    <button id="new-card-button" class="btn btn-primary">
                        Save Method
                    </button>

                </div>
            </div>
        </form>
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

          let newCard = elements.create('card', {style: style})
          newCard.mount('#new-card-element')
          let newPaymentMethod = null
          $('#new-payment-method-form').on('submit', function (e) {
              $('#new-card-button').prop('disabled',true);
              if (newPaymentMethod) {
                  return true
              }
              stripe.confirmCardSetup(
                  "{{ $intent->client_secret }}",
                  {
                      payment_method: {
                          card: newCard,
                          billing_details: {name: $('#new-card-holder-name').val()}
                      }
                  }
              ).then(function (result) {
                  if (result.error) {
                      console.log(result)
                      alert('error')
                  } else {
                      newPaymentMethod = result.setupIntent.payment_method
                      $('#new_payment_method').val(newPaymentMethod)
                      $('#new-payment-method-form').submit()
                  }
              })
              return false
          })

          $('.button-new-payment-method').on('click', function (e) {
              e.preventDefault();
              $('#new-checkout-block').removeClass('d-none');
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