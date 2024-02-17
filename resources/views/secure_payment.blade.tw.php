@extends('public.header')

@section('content')

<section class="hero background hero-secure center" data-speed="2" data-type="background">
  <div class="container mx-auto">
    <div class="flex flex-wrap">
      <h1>Secure Payment</h1>
      <p class="thin"><img src="{{ asset('images/icon-secure-pay.png') }}">256-BiT Encryption</p>
      <img src="{{ asset('images/providers.png') }}">
    </div>
  </div>
</section>

<section class="secure">
  <div class="container mx-auto">
    <div id="secure-form" class="flex flex-wrap">          
      <div class="md:w-3/5 pr-4 pl-4 info">
        <form>
          <div class="flex flex-wrap">
            <div class="mb-4 md:w-1/2 pr-4 pl-4">
              <label for="firstname">First Name</label>
              <input type="text" class="block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-grey-darker border border-grey rounded" id="firstname" name="firstname">
              <span class="help-block" style="display: none;">Please enter your first name.</span>

            </div>
            <div class="mb-4 md:w-1/2 pr-4 pl-4">
              <label for="lastname">Last name</label>
              <input type="text" class="block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-grey-darker border border-grey rounded" id="lastname" name="lastname">
              <span class="help-block" style="display: none;">Please enter your last name.</span>
            </div>
          </div>

          <div class="flex flex-wrap">
            <div class="mb-4 md:w-full pr-4 pl-4">
              <label for="streetadress">Street Address</label>
              <input type="text" class="block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-grey-darker border border-grey rounded" id="streetadress" name="streetadress">
              <span class="help-block" style="display: none;">Please enter addess.</span>

            </div>
          </div>

          <div class="flex flex-wrap">
            <div class="mb-4 md:w-1/4 pr-4 pl-4">
              <label for="apt">Apt/Ste</label>
              <input type="text" class="block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-grey-darker border border-grey rounded" id="apt" name="apt">
              <span class="help-block" style="display: none;">Please enter your Apt/Ste.</span>
            </div>

            <div class="mb-4 md:w-1/4 pr-4 pl-4">
              <label for="apt">City</label>
              <input type="text" class="block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-grey-darker border border-grey rounded" id="city" name="city">
              <span class="help-block" style="display: none;">Please enter your city.</span>
            </div>

            <div class="mb-4 md:w-1/4 pr-4 pl-4">
              <label for="apt">State/Province</label>
              <input type="text" class="block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-grey-darker border border-grey rounded" id="state" name="state">
              <span class="help-block" style="display: none;">Please enter your State/Province.</span>
            </div>

            <div class="mb-4 md:w-1/4 pr-4 pl-4">
              <label for="apt">Postal Code</label>
              <input type="text" class="block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-grey-darker border border-grey rounded" id="postal" name="postal">
              <span class="help-block" style="display: none;">Please enter your Postal Code.</span>
            </div>
          </div>

        </div>
        <div class="md:w-2/5 pr-4 pl-4">
          <div class="relative flex flex-col min-w-0 rounded break-words border bg-white border-1 border-grey-light">
            <div class="flex flex-wrap">
              <div class="mb-4 md:w-full pr-4 pl-4">
                <label for="streetadress">Card number</label>
                <input type="text" class="block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-grey-darker border border-grey rounded with-icon" id="cardnumber" name="cardnumber">
                <span class="glyphicon glyphicon-lock"></span>
                <span class="help-block" style="display: none;">Please enter your card number.</span>

              </div>
            </div>
            <div class="flex flex-wrap">
              <div class="mb-4 md:w-1/2 pr-4 pl-4">
                <label for="firstname">Expiration Month</label>
                <select class="block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-grey-darker border border-grey rounded" id="month" name="month">
                  <option>January</option>
                </select>
                <span class="help-block" style="display: none;">Please select the month.</span>

              </div>
              <div class="mb-4 md:w-1/2 pr-4 pl-4">
                <label for="firstname">Expiration year</label>
                <select class="block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-grey-darker border border-grey rounded" id="year" name="year">
                  <option>2016</option>
                </select>
                <span class="help-block" style="display: none;">Please select the year.</span>

              </div>
            </div>


            <div class="flex flex-wrap">
              <div class="mb-4 md:w-1/2 pr-4 pl-4">
                <label for="apt">CVV</label>
                <input type="text" class="block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-grey-darker border border-grey rounded" id="cvv" name="cvv">
                <span class="help-block" style="display: none;">Please enter the CVV.</span>
              </div>

              <div class="md:w-1/2 pr-4 pl-4">
                <p><span class="glyphicon glyphicon-credit-card" style="margin-right: 10px;"></span><a href="#">Where Do I find CVV?</a></p>
              </div>
            </div>
          </div>


        </div>
      </div>
      <div class="flex flex-wrap">
        <div class="md:w-full pr-4 pl-4">
          <button type="submit" id="feedbackSubmit" class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline text-blue-lightest bg-blue hover:bg-blue-light py-3 px-4 text-xl leading-tight green">PAY NOW - $2.00</button>
        </div>
      </form>
    </div>
  </div>
</div>


</section>


@stop