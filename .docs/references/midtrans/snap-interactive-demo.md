# Interactive Demo

Interactive Demonstration of Snap Integration

Step 1 and Step 2 in [Snap Integration Overview](/docs/snap) are explained here using interactive demonstration. You can also observe the source code and see the real-time output by trying the *Snap* integration demo below.

***

<br />

## Requirements

<br />

Midtrans Account and API keys will be used in this integration, but we will be using a predefined demo keys.

<br />

***

<br />

## Specifications

<br />

* **Backend**: This demo is using **NodeJS** (hosted on CodeSandbox) for simplicity, but you can use any backend language.
* **Frontend**: HTML and JavaScript.

<br />

***

<br />

## Usage Explanation

<br />

You will observe the basic implementation flow of creating payment page via *Snap API*.

1. Click **Proceed to Payment** to test the frontend.

<br />

> 📘 Note
>
> Please wait until the window/iframe below is fully loaded. It may take some time while it tries to build the backend.

<br />

**CodeSandbox demo Midtrans NodeJS**

<HTMLBlock>
  {`
  <iframe src="https://codesandbox.io/embed/snap-basic-integration-demo-forked-h8x2pz?fontsize=14&amp;hidenavigation=0&amp;theme=dark" width="100%" height="600px"></iframe>
  `}
</HTMLBlock>

<br />

2. Click **Open Sandbox** to view and edit the full source code. You can modify with the sample code or copy it as a reference to your local machine.

<br />

**Alternative Frontend Integration Sample**\
A sample frontend integration, hosted on JSFiddle is shown below.

1. Enter the value of `snap_transaction_token` in **Snap Token** field.
2. Click **Pay**.
3. Click **HTML** to see the source code.

<br />

<HTMLBlock>
  {`
  <iframe width="100%" height="750" src="https://jsfiddle.net/kntfdzob/embedded/result,html/dark" allowfullscreen="allowfullscreen" allowpaymentrequest="" frameborder="0"></iframe>
  `}
</HTMLBlock>

***

<br />

## Testing Payment

<br />

You can perform successful transaction by entering the card credentials given below.

<br />

| Name        | Value                                                 |
| ----------- | ----------------------------------------------------- |
| Card Number | `4811 1111 1111 1114`                                 |
| CVV         | `123`                                                 |
| Exp Month   | Any month in MM format. For example, `02`.            |
| Exp Year    | Any future year, in YYYY format. For example, `2025`. |
| OTP/3DS     | `112233`                                              |

<br />

For more test payment credentials, refer to [Testing Payments on Sandbox](/docs/testing-payment-on-sandbox).

<br />

***

<br />

## Testing in Midtrans Demo Store

<br />

You can also simulate how the transaction flow would look like in Snap Checkout via Midtrans Demo Store ([Pop Up & Redirection](https://demo.midtrans.com/), [Embedded](https://demo.sandbox.midtrans.com/)).

<br />

### Configuring the payment flow

<br />

By default, Demo Store will show all payment methods with one time payment flow if not configured. To explore other flows, after clicking 'Buy Now', configure what you want to customize in this step.

1. Configuring cart and customer details\
   You can modify the quantity and customer details by configuring it here. If you want to receive payment receipt emails as well make sure you change the email provided here to your email.

   <Image align="center" width="50% " src="https://files.readme.io/eff1eb9-image.png" />

2. To configure the payment methods, click the gear icon next to the `Checkout` button.

3. To proceed with standard setup, select the first option. Otherwise, click the `Set advanced rule` option.

   1. UI Method : choose between Pop Up mode (Snap Checkout window will be overlaid in the webpage) or Redirection mode (you will be redirected to a dedicated Snap Checkout page)
   2. Active Payment Channels : select Custom Select to manually choose what payment method to be shown in the payment list page. If you only choose 1 payment method, Snap Checkout will skip showing the payment list and jump directly to that specific payment method flow. Depending what payment method you choose here, other payment method settings may or may not show below.
   3. GoPay Tokenization Settings : will only appear if you choose GoPay in the previous step. Modify this if you want to simulate GoPay Tokenization payment flow. You can type in any values here as the user ID, as long as it hasn't been used before Snap Checkout will show you the linking flow when you first see the Snap Checkout page in Demo Store.
   4. Credit Card Settings : will only appear if you choose Card in the previous step. If you want to simulate the card recurring payment flow, make sure to choose the `1-Click` option in the `Recurring User` menu. You can also modify the 3DS Settings, Authorize flow, and Installment flow here. Leave everything else as default unless you have a specific scenario to simulate.
   5. ATM/Bank Transfer Settings : will only appear if you choose Bank Transfer in the previous step.  You can optionally modify the custom VA number and expiry time here.
   6. Merchant Settings : skip this step as this is a legacy feature.

Finally, click Start to invoke Demo Store's Snap Checkout page.

<br />

***

<br />

## Next Step

<br />

### Get Your Own API Keys

Sign up for Midtrans account and retrieve your API keys. Follow the steps [here](/docs/snap-preparation).

<br />

### Handling After-Payment Scenarios

Follow this [guide](/docs/snap-snap-integration-guide#4-handling-after-payment) after you have finished your integration to handle completed payments and implement advanced features.

<br />