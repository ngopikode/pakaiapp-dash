# Advanced Feature

Snap has various optional parameters. These optional parameters can be utilized for more advanced use cases that can help your integration.

# <b>General</b>

<br />

## Recommended Parameters

<br />

You can  include more information such as `customer_details`, `item_details`, and so on along side `transaction_details`. While sending API requests, it is recommended to send more details regarding the transaction, so that these details will be captured on the transaction record which can be [viewed on the Midtrans Dashboard](/docs/midtrans-dashboard-usage#transaction).

The recommended JSON parameters for general use are given below. These parameters are used during [backend API request step](/docs/snap-snap-integration-guide#1-acquiring-transaction-token-on-backend).

<br />

```json JSON Parameters
{
  "transaction_details": {
    "order_id": "CustOrder-102",
    "gross_amount": 13000
  },
  "credit_card": {
    "secure": true
  },
  "item_details": [
    {
      "id": "a01",
      "price": 7000,
      "quantity": 1,
      "name": "Apple"
    },
    {
      "id": "b02",
      "price": 3000,
      "quantity": 2,
      "name": "Orange"
    }
  ],
  "customer_details": {
    "first_name": "Budi",
    "last_name": "Susanto",
    "email": "budisusanto@example.com",
    "phone": "+628123456789",
    "billing_address": {
      "first_name": "Budi",
      "last_name": "Susanto",
      "email": "budisusanto@example.com",
      "phone": "08123456789",
      "address": "Sudirman No.12",
      "city": "Jakarta",
      "postal_code": "12190",
      "country_code": "IDN"
    },
    "shipping_address": {
      "first_name": "Budi",
      "last_name": "Susanto",
      "email": "budisusanto@example.com",
      "phone": "0812345678910",
      "address": "Sudirman",
      "city": "Jakarta",
      "postal_code": "12190",
      "country_code": "IDN"
    }
  }
}
```

```curl Sample Charge API Request
curl -X POST \
  https://app.sandbox.midtrans.com/snap/v1/transactions \
  -H 'Accept: application/json'\
  -H 'Authorization: Basic U0ItTWlkLXNlcnZlci1UT3ExYTJBVnVpeWhoT2p2ZnMzVV7LZU87' \
  -H 'Content-Type: application/json' \
  -d '{
  "transaction_details": {
    "order_id": "CustOrder-102",
    "gross_amount": 13000
  },
  "credit_card": {
    "secure": true
  },
  "item_details": [
    {
      "id": "a01",
      "price": 7000,
      "quantity": 1,
      "name": "Apple"
    },
    {
      "id": "b02",
      "price": 3000,
      "quantity": 2,
      "name": "Orange"
    }
  ],
  "customer_details": {
    "first_name": "Budi",
    "last_name": "Susanto",
    "email": "budisusanto@example.com",
    "phone": "+628123456789",
    "billing_address": {
      "first_name": "Budi",
      "last_name": "Susanto",
      "email": "budisusanto@example.com",
      "phone": "08123456789",
      "address": "Sudirman No.12",
      "city": "Jakarta",
      "postal_code": "12190",
      "country_code": "IDN"
    },
    "shipping_address": {
      "first_name": "Budi",
      "last_name": "Susanto",
      "email": "budisusanto@example.com",
      "phone": "0812345678910",
      "address": "Sudirman",
      "city": "Jakarta",
      "postal_code": "12190",
      "country_code": "IDN"
    }
  }
}'
```

For more details, refer to [Snap Docs](/reference/json-object-overview).

You can also [add fee, tax, discount, etc. to item\_details](/docs/technical-faq#how-should-i-include-internal-fee-tax-discount-in-item_details-api-params) if you need.

<br />

***

<br />

## Configuring Redirect URL

Redirect URL is used to redirect your customer after the payment process is complete through Snap.

<br />

### **Snap Popup (Default)**

<br />

This is the default method. In this method, payment page is embedded in merchant website/app. To set your redirect URL, in pop-up mode, go to [**Settings - Snap Preference - System Settings**](https://dashboard.sandbox.midtrans.com/settings/snap_preference).

<br />

![](https://files.readme.io/668a6ec-snap-prep-redirect-url-snapjs.png "snap-prep-redirect-url-snapjs.png")

<br />

The diagram given below explains the working of redirect URL.

<br />

![](https://files.readme.io/7d7bc5f-snap-prep-diagram-snapjs.png "snap-prep-diagram-snapjs.png")

<br />

### **Snap Redirect**

<br />

In this method, the customer will be redirected to Midtrans hosted payment page. To set your redirect URL in redirect mode, go to [**Settings - Snap Preference - System Settings**](https://dashboard.sandbox.midtrans.com/settings/snap_preference).

<br />

<Image title="snap-prep-redirect-url-snapredir.png" align="center" src="https://files.readme.io/ede9a31-snap-prep-redirect-url-snapjs.png" />

<br />

The diagram given below explains the working of redirection URLs.

<br />

![](https://files.readme.io/0c4d1db-snap-prep-diagram-snapredir.png "snap-prep-diagram-snapredir.png")

> 📘 Note
>
> The final redirect URL is appended with query parameter like `?order_id=xxx&status_code=xxx&transaction_status=xxx`.

<br />

The final redirect URL may look like this: `https://tokoecommerce.com/finish_payment/?order_id=CustOrder-102123123&status_code=200&transaction_status=capture`.

<br />

You may use this information to display custom message to your customer on your finish URL.

<br />

> 🚧
>
> Specific if the payment method is Credit Card & processed via [3DS 2](https://api-docs.midtrans.com/#card-feature-3d-secure-3ds) (when the acquiring bank and the MID support), there's small possibility of the transaction is still waiting for the card's 3DS provider to process/verify it, which then Snap will trigger redirect with `?transaction_status=pending` instead of `capture/settlement`. To handle the payment success update, as usual you should [handle HTTP Notification](/docs/snap-snap-integration-guide#4-handling-after-payment).

<br />

***

<br />

## Snap.js Function and Options

<br />

Snap.js supports various useful options, such as specifying language, specifying GoPay payment mode to deeplink, and so on. You can use these options according to your requirement.

For more details, refer to [Snap Docs](/reference/frontend-integration-overview).

<br />

> 📘 Note
>
> If you are using Snap Redirect mode, you can append options as Query parameter at the end of the Snap `redirect_url`:

<br />

```text
[redirect_url]?[options1]=[value]&[options2]=[value]
```

Which for example (using both `language=en` & `gopayMode=deeplink` options) the final result URL is shown below.

<br />

```text

https://app.sandbox.midtrans.com/snap/v2/vtweb/cf9534e3-ddf7-43f9-a1b7-5f618d2d1c96?language=en&gopayMode=deeplink
```

<br />

***

<br />

## Snap.js main functions

<br />

Including Snap.js on your frontend will expose `snap` object as global JS object accessible with `window.snap`. These are some main functions that you can access from your frontend codes:

* `window.snap.pay('SNAP_TRANSACTION_TOKEN', options)`: Will open payment page for that specific Snap Token, which is tied to specific Order ID. Also useful if you want to re-open that same payment page again e.g. when you allow another attempt of payment for that same Order ID, in case of closed earlier. Second parameter is optional `options` object [explained further here](/reference/snap-js).
* `window.snap.hide()`: Will close the Snap payment page popup. Useful if you want to close the payment page from customers, e.g. when you/customers want to cancel the payment.

For more details, refer to [Snap Docs](/reference/frontend-integration-overview).

<br />

***

<br />

## JavaScript Callback

<br />

Snap.js supports callbacks. It can be used to trigger your custom JavaScript implementation on each event. The available callbacks are given below.

* `onSuccess`: Function that will be triggered when payment is successful.
* `onPending`: Function that will be triggered when payment is pending, which is for payment that requires further customer action, such as bank transfer / VA, or waiting for 3DS/OTP process\*.
* `onError`: Function that will be triggered when there is a payment failure after several attempts.
* `onClose`: Function that will be triggered when customer has closed the Snap popup.

<br />

Example of the Snap.js callback option usage while calling `window.snap.pay(...)` is given below. This parameter is used during [Snap frontend implementation](/docs/snap-snap-integration-guide#2-displaying-snap-payment-page-on-frontend).

<br />

```javascript Frontend JS
window.snap.pay('SNAP_TRANSACTION_TOKEN', {
  onSuccess: function(result){
    /* You may add your own implementation here */
    alert("payment success!"); console.log(result);
  },
  onPending: function(result){
    /* You may add your own implementation here */
    alert("wating your payment!"); console.log(result);
  },
  onError: function(result){
    /* You may add your own implementation here */
    alert("payment failed!"); console.log(result);
  },
  onClose: function(){
    /* You may add your own implementation here */
    alert('you closed the popup without finishing the payment');
  }
})
```

For more details on Snap JS callback's result JSON properties & the samples, refer to [this section of Snap Docs](/reference/js-callback).

<br />

> 🚧
>
> Specific if the payment method is Credit Card & processed via [3DS 2](/reference/card-feature-3d-secure-3ds) (when the acquiring bank and the MID support), there's small possibility of the transaction is still waiting for the card's 3DS provider to process/verify it, which then Snap will trigger `onPending` callback instead of `onSuccess`. To handle the payment success update, as usual you should [handle HTTP Notification](/docs/snap-snap-integration-guide#4-handling-after-payment).

<br />

***

<br />

## Custom Finish URL

<br />

> 📘 Supported Payment Method
>
> This feature is only applicable for payment methods where customer <b>will need to finish trx in Partner's payment page such as Internet Banking and BNPL.</b>
>
> Except for Kredivo, you can specify the finish URL either via dashboard or API request. Kredivo's finish URL can only be specified via dashboard.

<br />

Use this feature if you want to set a custom finish URL for customers to be redirected to after generating a payment code or making a payment. By default, Snap will redirect the customer to [Finish Redirect URL configured on Dashboard](#configuring-redirect-url). But you can override that configuration by specifying `callbacks.finish` parameter. This will allow you to have specific redirect for each specific payment.

Example of the JSON parameters used during [backend API request step](/docs/snap-snap-integration-guide#1-acquiring-transaction-token-on-backend) is given below.

<br />

```json JSON Parameters
{
  "transaction_details": {
    "order_id": "CustOrder-102",
    "gross_amount": 13000
  },
  "callbacks": {
    "finish": "https://tokoecommerce.com/my_custom_finish/?name=Customer01"
  }
}
```

```curl Sample Charge API Request
curl -X POST \
  https://app.sandbox.midtrans.com/snap/v1/transactions \
  -H 'Accept: application/json'\
  -H 'Authorization: Basic U0ItTWlkLXNlcnZlci1UT3ExYTJBVnVpeWhoT2p2ZnMzVV7LZU87' \
  -H 'Content-Type: application/json' \
  -d '{
  "transaction_details": {
    "order_id": "CustOrder-102",
    "gross_amount": 13000
  },
  "callbacks": {
    "finish": "https://tokoecommerce.com/my_custom_finish/?name=Customer01"
  }
}'
```

<br />

***

<br />

## Use your own payment list / checkout page

<br />

On Snap payment selection screen, all available payments for the merchant are activated by default. Under some conditions, you may need to display only some of the payment methods available, or show your own payment list instead of using Snap Checkout's payment list. There are two ways to achieve this, which are given below.

<br />

### A) Specify Payment Channel via Dashboard

<br />

You can configure Enable Payment with Snap Preference on Midtrans *Dashboard*. This will apply to all Snap transactions for the Merchant account.

1. Login to your Midtrans Dashboard.
2. Go to **Settings - Snap Preferences - Payment Channels Tab**
3. Click \[x] icon to disable payment channel.
4. Click \[+] icon To enable payment channel.
5. Click **Apply Recommended Sorting** to use Midtrans's recommended sorting. Or drag/drop manually to sort payment channel.
6. Click **Save**.

<br />

![](https://files.readme.io/946f92f-snap-adv-enabled-payment-dash.png "snap-adv-enabled-payment-dash.png")

<br />

> 📘 Note
>
> The configuration that is set using this method will only be applied to Snap payment transaction that is being opened via Snap Popup method (frontend javascript implementation method with snap.js). If your implementation is via Snap Redirect mode, please use configuration on the next section.

> 👍 Apply Recommended Sorting
>
> It is highly recommended to apply recommended sorting to sort your payment list presentment - Recommended Sorting will sort out your payment method based on popular payment methods which will help to increase your payment conversion rate.

<br />

### B) Specify Payment Channel via API Request

<br />

> 📘 Usecases
>
> Popular usecases for specifying payment channel via API requests are as follow :
>
> * You maintain your own payment list and want to map each payment method to Snap checkout page containing said payment method
> * You integrate Snap to multiple website/app/usecases and want to differentiate the available payment method without having to create multiple Midtrans Merchant ID.

<br />

You can add and customize `enabled_payments` parameter. That will apply specifically for the transaction. If you only specify 1 payment method in the `enabled_payments` parameter, **Snap Checkout will not show the payment list page, and go directly to respective payment method's flow instead.**

Example of the JSON parameters used during [backend API request step](/docs/snap-snap-integration-guide#1-acquiring-transaction-token-on-backend) is given below.

<br />

```json JSON Parameters
...
"enabled_payments": [
    "credit_card",
    "gopay",
    "shopeepay",
    "permata_va",
    "bca_va",
    "bni_va",
    "bri_va",
    "echannel",
    "other_va",
    "Indomaret",
    "alfamart",
    "akulaku"
]
...
```

```curl Sample Charge API Request
curl -X POST \
  https://app.sandbox.midtrans.com/snap/v1/transactions \
  -H 'Accept: application/json'\
  -H 'Authorization: Basic U0ItTWlkLXNlcnZlci1UT3ExYTJBVnVpeWhoT2p2ZnMzVV7LZU87' \
  -H 'Content-Type: application/json' \
  -d '{
  "transaction_details": {
    "order_id": "CustOrder-102",
    "gross_amount": 13000
  },
  "enabled_payments": [
    "credit_card",
    "gopay",
    "shopeepay",
    "permata_va",
    "bca_va",
    "bni_va",
    "bri_va",
    "echannel",
    "other_va",
    "Indomaret",
    "alfamart",
    "akulaku"
  ],
  "credit_card": {
    "secure": true
  },
  "customer_details": {
    "first_name": "Budi",
    "last_name": "Susanto",
    "email": "budisusanto@example.com",
    "phone": "+628123456789"
  }
}'
```

`enabled_payments` also support alias, which refers to a list of payment types. Adding an alias is the equivalent of adding all the payment types it refers to. Supported aliases:

* `bank_transfer` = `permata_va, bca_va, bni_va, bri_va, echannel`
* `store` = `kioson, indomaret, alfamart`.

Use of `enabled_payments` is shown in the sample CURL request shown below.

<br />

```curl Sample Charge API Request
curl -X POST \
  https://app.sandbox.midtrans.com/snap/v1/transactions \
  -H 'Accept: application/json'\
  -H 'Authorization: Basic U0ItTWlkLXNlcnZlci1UT3ExYTJBVnVpeWhoT2p2ZnMzVV7LZU87' \
  -H 'Content-Type: application/json' \
  -d '{
  "transaction_details": {
    "order_id": "CustOrder-102",
    "gross_amount": 9000
  },
  "enabled_payments": [
    "credit_card"
  ],
  "credit_card": {
    "secure": true
  }
}'
```

This code will display only the credit card payment method on Snap as shown below.

<br />

![](https://files.readme.io/01c3890-snap-adv-enabled-payment.png "snap-adv-enabled-payment.png")

<br />

***

<br />

## Custom Transaction Expiry

<br />

Custom Expiry feature enables you to set an expiry time of payment for each transaction. When the time elapses, customer will no longer be able to pay the transaction. There are two ways to configure expiry time, which are given below.

<br />

### A) Custom Expiry via Dashboard

<br />

You can set custom expiry with Snap Preference on Midtrans Dashboard. This will apply to all Snap transactions for the Merchant account. You can customize both Snap page's expiry and individual Payment Method's expiry.

1. Login to your MAP account.
2. On the home page, go to **Settings- Snap Preferences**. *Snap Preferences* page is displayed.
3. Select **System Settings** tab.
4. Select **Default** check box.
5. Select duration unit from the drop-down menu.
6. Click **Save**.

<br />

![](https://files.readme.io/34344bf-snap-adv-custom-expiry-dash.png "snap-adv-custom-expiry-dash.png")

<br />

### B) Custom Expiry via API Request

<br />

This method is used to configure expiry time for a specific transaction.

Example of the JSON parameters used during [backend API request step](/docs/snap-snap-integration-guide#1-acquiring-transaction-token-on-backend) is given below.

<br />

```json JSON Parameters
...
  "expiry": {
    "start_time": "2020-04-13 18:11:08 +0700",
    "unit": "minutes",
    "duration": 180
  }
...
```

```curl Sample Charge API Request
curl -X POST \
  https://app.sandbox.midtrans.com/snap/v1/transactions \
  -H 'Accept: application/json'\
  -H 'Authorization: Basic U0ItTWlkLXNlcnZlci1UT3ExYTJBVnVpeWhoT2p2ZnMzVV7LZU87' \
  -H 'Content-Type: application/json' \
  -d '{
  "transaction_details": {
    "order_id": "CustOrder-102",
    "gross_amount": 13000
  },
  "expiry": {
    "start_time": "2020-04-13 18:11:08 +0700",
    "unit": "minutes",
    "duration": 180
  }
}'
```

| Parameter    | Description                                                                                                                                                                                       | Type        | Required   |
| ------------ | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------- | ---------- |
| `start_time` | Timestamp in `YYYY-MM-DD HH:MM:SS +0700`.<br />If not specified, transaction time will be used as start time (when customer confirm payment channel). Time Zone is Western Indonesian Time (WIT). | String(255) | Optional\* |
| `duration`   | Expiry duration                                                                                                                                                                                   | Integer     | Required   |
| `unit`       | Expiry unit. Options: `day, hour, minute` (plural term also accepted).                                                                                                                            | String      | Required   |

<br />

> 📘
>
> The `start_time` parameter is optional, but if you don’t include it, asynchronous payment methods like Bank Transfer, GoPay, etc. (payment methods that have `pending` status) will start the expiry duration only when the customer has proceeded to select & confirm payment on Snap payment screen. Thus the total expiry duration may not be as you were expecting due to Snap payment screen not immediately completed by customer. To avoid this, it is recommended to include the `start_time` on the API request.

<br />

### C) Custom Page Expiry via API Request

<br />

Specify during Snap token creation via API request. Page expiry is **optional**.

<br />

#### Request Body (JSON Parameters)

```json JSON Parameters
...
"page_expiry": {
    "duration": 3,
    "unit": "hours"
}
...
```

```curl Sample Charge API Request
curl -X POST \
  https://app.sandbox.midtrans.com/snap/v1/transactions \
  -H 'Accept: application/json'\
  -H 'Authorization: Basic U0ItTWlkLXNlcnZlci1UT3ExYTJBVnVpeWhoT2p2ZnMzVV7LZU87' \
  -H 'Content-Type: application/json' \
  -d '{
  "transaction_details": {
    "order_id": "CustOrder-102",
    "gross_amount": 13000
  },
  "page_expiry": {
    "duration": 3,
    "unit": "hours"
  }
}'
```

| Parameter  | Description                                                             | Type    | Required |
| ---------- | ----------------------------------------------------------------------- | ------- | -------- |
| `duration` | Page expiry duration in numbers                                         | Integer | Required |
| `unit`     | Expiry unit. Options: `day, hour, minute` (plural terms also accepted). | String  | Required |

<br />

***

<br />

## Custom Fields

<br />

Custom fields allow you to send your own (custom) data to Snap API, and then it will be sent back from Midtrans to your backend on HTTP notification. It will be displayed on Dashboard under the *order detail*.

Example of the JSON parameters used during [backend API request step](/docs/snap-snap-integration-guide#1-acquiring-transaction-token-on-backend) is given below.

<br />

```json JSON Parameters
...
  "custom_field1": "this is custom text defined by merchant",
  "custom_field2": "order come from web",
  "custom_field3": "customer selected blue color variant"
...
```

```curl Sample Charge API Request
curl -X POST \
  https://app.sandbox.midtrans.com/snap/v1/transactions \
  -H 'Accept: application/json'\
  -H 'Authorization: Basic U0ItTWlkLXNlcnZlci1UT3ExYTJBVnVpeWhoT2p2ZnMzVV7LZU87' \
  -H 'Content-Type: application/json' \
  -d '{
  "transaction_details": {
    "order_id": "CustOrder-102",
    "gross_amount": 13000
  },
  "custom_field1": "this is custom text defined by merchant",
  "custom_field2": "order come from web",
  "custom_field3": "customer selected blue color variant"
}'
```

| Parameter      | Description                                                                              | Type        | Required |
| -------------- | ---------------------------------------------------------------------------------------- | ----------- | -------- |
| custom\_field1 | Custom field 1 for custom parameter from merchant. <br />Input any information you want. | String(255) | Optional |
| custom\_field2 | Custom field 2 for custom parameter from merchant. <br />Input any data you want.        | String(255) | Optional |
| custom\_field3 | Custom field 3 for custom parameter from merchant.<br />Input any data you want.         | String(255) | Optional |

<br />

***

<br />

## Customize Themes

<br />

You can customize Snap's look and feel to make the checkout UI blend more seamlessly to your web/app's page. Brand name, header color, logo, and page language can be customized.

If you're using Snap Pop Up, go to [Snap Preference](https://dashboard.midtrans.com/settings/snap_preference) > Theme and Logo to manage the configurations.

If you're using Snap Redirection, go to [VT-Web Preference](https://dashboard.midtrans.com/settings/vtweb_preference) to manage the configurations.

<br />

***

<br />

## Automatic Fee Imposition

<br />

With this feature, you can apply the MDR and Transaction Fee to customers for specific payment methods, either fully or partially. Snap will automatically calculate and add the applicable fee to the transaction, ensuring that once the customer completes the payment, the merchant receives the amount based on the imposed percentage and the gross transaction value.

For example, when merchant creates a transaction using QRIS with a gross amount of Rp100,000 and an MDR of 0.7%, Snap will add Rp705 to cover the fee. This brings the total transaction amount to Rp100,705. When the customer completes the payment, the merchant will receive Rp100,705 × (1 - 0.7%) = Rp100,000.

You can enable this feature via Snap Preference or per transaction when you create a Snap token.

<br />

```json Sample Request on Create Token
{
  ...
  "customer_imposed_payment_fee": {
    "enable": true,
    "payment_fee_configs": [
      {
        "payment_type": "gopay",
        "customer_percentage": 100
      }
    ]
  },
  ...
}
```

```Text Sample HTTP Notif
{
  "extra_info": {
    "gross_amount_info": {
      "original_amount": "10000",
      "gross_amount": 10071,
      "customer_imposed_payment_fee": "71"
    }
  }
}
```

<br />

<br />

<Table align={["left","left","left","left"]}>
  <thead>
    <tr>
      <th>
        Parameter
      </th>

      <th>
        Description
      </th>

      <th>
        Type
      </th>

      <th>
        Required
      </th>
    </tr>
  </thead>

  <tbody>
    <tr>
      <td>
        `enable`
      </td>

      <td>
        Determine whether the transaction will use impose fee. When toggle in Snap Preference is activated, but the value on create token is set to `false`, the transaction won't impose any fee to customer.
      </td>

      <td>
        Boolean
      </td>

      <td>
        Required
      </td>
    </tr>

    <tr>
      <td>
        `payment_fee_configs`
      </td>

      <td>
        Array of payment fee configuration.\
        When you activated several `payment_type` in Snap preference and then specify only some of them here, only the one specified on the request will be activated.
      </td>

      <td>
        Array of Object
      </td>

      <td>
        * Required if `enable` is `true`
      </td>
    </tr>

    <tr>
      <td>
        `payment_fee_configs.*.payment_type`
      </td>

      <td>
        Payment method
      </td>

      <td>
        String
      </td>

      <td>
        Required
      </td>
    </tr>

    <tr>
      <td>
        `payment_fee_configs.*.customer_percentage`
      </td>

      <td>
        Percentage of fee that getting imposed to customer. Value must be between 0 to 100
      </td>

      <td>
        Numeric
      </td>

      <td>
        Required
      </td>
    </tr>
  </tbody>
</Table>

<br />

<br />

> 🚧 Note
>
> * Supported Payment Method :
>   * GoPay Deeplink & QRIS
>   * Shopeepay & QRIS
>   * Virtual Account
>   * Dana
> * For certain payment methods with more complex fee structures, the final received amount may not exactly match the original gross amount.
> * Admin Fee for `other_qris` and `other_va` will follow the fee of the Acquirer.
>   * `other_qris` with Gopay as the acquirer will use Gopay QRIS fee.
>   * `other_va` with Permata as the acquirer will use Permata VA fee.
> * When a fee for a payment type is updated, It will took some time until the next midnight 00:00AM for the new fee to be applied on the transaction.
> * CMS user is not yet supported at this point.

<br />

### Fee Imposition on Promo Transaction

Fee imposition on transaction with promo will be calculated after the gross amount deducted by discount amount from the promo.

For example, when merchant creates a transaction using QRIS with a gross amount of Rp100,000, MDR of 0.7%, and Promo with amount Rp5,000, Snap will add Rp670 to cover the fee. This brings the total transaction amount to Rp95,670. When the customer completes the payment, the merchant will receive Rp95,670 × (1 - 0.7%) = Rp95,000.

<br />

### Fee Imposition on Shopify

Merchant that use Shopify platform and enable fee imposition will see the original amount in Shopify Dashboard although the final amount that customer paid is including the MDR fee.

<br />

## Customer Data Collection

<br />

The `customer_details` object in the transaction payload is used to collect and manage customer information during the payment process. This includes personal and contact information that can be required, optional, or conditional based on your business rules.

Key fields that control customer data collection :

| Field            | Possible Values                                 | Description                                                                           |
| ---------------- | ----------------------------------------------- | ------------------------------------------------------------------------------------- |
| collect\_email   | required, optional, none, null, or not included | Determines whether the customer's email is collected. Saved for notifications.        |
| collect\_name    | required, optional, none, null, or not included | Determines whether the customer's name is collected. Saved for notifications.         |
| collect\_address | required, optional, none, null, or not included | Determines whether the customer's address is collected. Saved for notifications.      |
| collect\_phone   | required, optional, none, null, or not included | Determines whether the customer's phone number is collected. Saved for notifications. |

<br />

> 🚧 Note
>
> * If a field is required, it appears on the first page of the Customer Details form and must be filled.
> * Optional fields (optional) can be skipped by the customer.
> * Fields with none, null, or not included at all are not collected.
> * Collected data is persisted and included in:
>   * Email notifications to the merchant.
>   * HTTP notifications (webhooks) for backend processing.

<br />

Example:

```json JSON Parameters
{
  ...
  "customer_details": {
    "collect_name": "required",
    "collect_address": "optional",
    "collect_email": "none",
    "collect_phone": null, // or not included
    "first_name": "Budi",
    "last_name": "Susanto",
    "email": "budisusanto@example.com",
    "phone": "+628123456789",
    "billing_address": {
      "first_name": "Budi",
      "last_name": "Susanto",
      "email": "budisusanto@example.com",
      "phone": "08123456789",
      "address": "Sudirman No.12",
      "city": "Jakarta",
      "postal_code": "12190",
      "country_code": "IDN"
    },
    "shipping_address": {
      "first_name": "Budi",
      "last_name": "Susanto",
      "email": "budisusanto@example.com",
      "phone": "0812345678910",
      "address": "Sudirman",
      "city": "Jakarta",
      "postal_code": "12190",
      "country_code": "IDN"
    }
  }
  ...
}
```

This ensures the merchant collects only the necessary customer information while saving it for notifications and backend processing.

<br />

### Handle Notification

When a transaction status changes, Midtrans sends an HTTP notification (webhook) to the merchant server. This notification includes transaction details as well as the `customer_details` if they were collected.

```json JSON Parameters
{
  "transaction_time": "2020-01-09 18:27:19",
  "transaction_status": "capture",
  "transaction_id": "57d5293c-e65f-4a29-95e4-5959c3fa335b",
  "status_message": "midtrans payment notification",
  "status_code": "200",
  "signature_key": "16d6f84b2fb0468e2a9cf99a8ac4e5d803d42180347aaa70cb2a7abb13b5c6130458ca9c71956a962c0827637cd3bc7d40b21a8ae9fab12c7c3efe351b18d00a",
  "payment_type": "credit_card",
  "order_id": "Postman-1578568851",
  "merchant_id": "G141532850",
  "masked_card": "48111111-1114",
  "gross_amount": "10000.00",
  "fraud_status": "accept",
  "eci": "05",
  "currency": "IDR",
  "channel_response_message": "Approved",
  "channel_response_code": "00",
  "card_type": "credit",
  "bank": "bni",
  "approval_code": "1578569243927",
  "customer_details": {
    "full_name": "test",
    "email": "test@test.test",
    "phone": "0813982200123",
    "address": "test test test"
  }
}

```

<br />

<br />

***

<br />

# <b>Credit Card</b>

<br />

## 3 Domain Secure (3DS)

<br />

Three Domain Secure (3DS) feature can be enabled/disabled for specific transactions on Snap. By default you **should always enable 3DS whenever possible**, to prevent unnecessary security and chargeback risks. Understand the risks in disabling 3DS. To disable 3DS for specific business needs, you require agreement and approval. Consult your Midtrans's Sales PIC or Support team to allow disabling 3DS.

* To enable 3DS, set the `secure` value to `true`.
* To disable 3DS, set the `secure` value to `false`.

<br />

Example of the JSON parameters used during [backend API request step](/docs/snap-snap-integration-guide#1-acquiring-transaction-token-on-backend) is given below.

<br />

```json JSON Parameters
...
  "credit_card": {
    "secure": true
  }
...
```

```curl Sample Charge API Request
curl -X POST \
  https://app.sandbox.midtrans.com/snap/v1/transactions \
  -H 'Accept: application/json'\
  -H 'Authorization: Basic U0ItTWlkLXNlcnZlci1UT3ExYTJBVnVpeWhoT2p2ZnMzVV7LZU87' \
  -H 'Content-Type: application/json' \
  -d '{
  "transaction_details": {
    "order_id": "CustOrder-102",
    "gross_amount": 9000
  },
  "credit_card": {
    "secure": true
  }
}'
```

<br />

***

<br />

## Save Card for Seamless Subsequent Payments

<br />

You can allow customer to save their card credentials within Snap payment page, for easier and faster future transactions. Card credentials will be saved securely on Midtrans side, and will not require merchant to manage the card data.

Merchant will only need to store and associate each unique customer with unique `user_id` defined by merchant.

* Add `"save_card" : true` and `user_id`

<br />

Example of the JSON parameters used during [backend API request step](/docs/snap-snap-integration-guide#1-acquiring-transaction-token-on-backend) is given below.

<br />

```json JSON Parameters
{
  "transaction_details": {
    "order_id": "CustOrder-102",
    "gross_amount": 9000
  },
  "credit_card": {
    "secure": true,
    "save_card": true
  },
  "user_id": "budiSusanto201"
}
```

```curl Sample Charge API Request
curl -X POST \
  https://app.sandbox.midtrans.com/snap/v1/transactions \
  -H 'Accept: application/json'\
  -H 'Authorization: Basic U0ItTWlkLXNlcnZlci1UT3ExYTJBVnVpeWhoT2p2ZnMzVV7LZU87' \
  -H 'Content-Type: application/json' \
  -d '{
  "transaction_details": {
    "order_id": "CustOrder-102",
    "gross_amount": 9000
  },
  "credit_card": {
    "secure": true,
    "save_card": true
  },
  "user_id": "budiSusanto201"
}'
```

<br />

> 📘 Note
>
> During the first payment when a customer is intended to save their card, **they must tick the “Save this card” check box on Snap payment page**, in order for the card to be saved. This is to **ensure that the customer explicitly gives consent** that they are ok that their card will be securely saved.

<br />

<br />

Use the same `user_id` API params for that particular customer on future transactions. Their card will be previewed on Snap card payment page, on future transactions.

<br />

![](https://files.readme.io/b058abe-snap-adv-save-card-preview.jpeg "snap-adv-save-card-preview.jpeg")

<br />

For more use cases, refer to [One Click, Two Click, and Recurring Transaction](https://support.midtrans.com/hc/en-us/articles/360002419153-One-Click-Two-Clicks-and-Recurring-Transaction).

<br />

***

<br />

## Recurring / Subscription Card Transaction (Tokenization)

<br />

> 📘
>
> This feature is also known as Card Tokenization. Which simply means a method to replace sensitive information value (card credentials) with a `token` (a placeholder) value, but also allowing it to still be associated with the actual value. Generally to keep sensitive information safely kept within secure environments, but still accessible for authorized parties.

<br />

Snap can be utilized to **initialize** subscription or recurring payment flow. You can choose to use either:

* [Core API](/docs/custom-interface-core-api) to do the recurring charge (continue read below).
* Or alternatively can charge recurring payments by utilizing our Subscription API to perform the subsequent charges. [Read more about our Subscription API here](https://docs.midtrans.com/reference/api-methods-1).

If you choose to use Core API to do the recurring charge:

* The recurring charge should be scheduled & triggered by your (merchant's) system/backend.
* Currently, recurring transaction supports only card transactions.

Please refer to the sequence diagram given below to understand the suggested flow.

<br />

<details>
  <summary><b>Snap Recurring Sequence Diagram</b></summary>

  <article>
    ![](https://files.readme.io/3dbeac1-snap-adv-snap-recurring-sequence.png "snap-adv-snap-recurring-sequence.png")
  </article>
</details>

<br />

Example of the JSON param for the first or initial transaction is given below. This param is used during [backend API request step](/docs/snap-snap-integration-guide#1-acquiring-transaction-token-on-backend).

<br />

```json JSON Parameters
{
  "transaction_details": {
    "order_id": "CustOrder-102",
    "gross_amount": 9000
  },
  "credit_card": {
    "secure": true,
    "save_card": true
  },
  "user_id": "budiSusanto201"
}
```

```curl Sample Charge API Request
curl -X POST \
  https://app.sandbox.midtrans.com/snap/v1/transactions \
  -H 'Accept: application/json'\
  -H 'Authorization: Basic U0ItTWlkLXNlcnZlci1UT3ExYTJBVnVpeWhoT2p2ZnMzVV7LZU87' \
  -H 'Content-Type: application/json' \
  -d '{
  "transaction_details": {
    "order_id": "CustOrder-102",
    "gross_amount": 9000
  },
  "credit_card": {
    "secure": true,
    "save_card": true
  },
  "user_id": "budiSusanto201"
}'
```

<br />

Then you will proceed with [displaying Snap payment page](/docs/snap-snap-integration-guide#2-displaying-snap-payment-page-on-frontend) as usual. After customer proceed with payment and result in successful first transaction, you will receive `saved_token_id` & `saved_token_id_expired_at` in the JSON of HTTP notification. `saved_token_id` is unique for each customer's card. Store this `saved_token_id` in your database and associate that card token to your customer.

<br />

> ❗️ Important
>
> Be sure to store the card's `saved_token_id_expired_at`. When that date time has been surpassed, the card's `saved_token_id` will be no longer usable, it means the card is expired. In that case you will need to ask your customer to re-do the card saving process with their re-newed card.

<br />

### Sample HTTP Notification with `saved_token_id`

<br />

```json
{
  "saved_token_id":"481111xDUgxnnredRMAXuklkvAON1114",
  "saved_token_id_expired_at": "2020-12-31 07:00:00",
  "status_code": "200",
  "status_message": "Success, Credit Card transaction is successful",
  ...
}
```

When you want to charge that particular customer, you will need to proceed with [Charge API request via Core API](/docs/coreapi-advanced-features#charge-api-request-for-recurring-transactions). The recurring transaction is non 3DS and will directly deduct customer's fund associated with the card.

<br />

> 📘 Note
>
> This feature requires special MID from acquiring bank, this utilize what bank usually call as "recurring MID". Which may means additional business agreement with the acquiring bank, you should consult Midtrans Activation team to activate this feature.

<br />

For more use cases, refer to [One Click, Two Click, and Recurring Transaction](https://support.midtrans.com/hc/en-us/articles/360002419153-One-Click-Two-Clicks-and-Recurring-Transaction).

<br />

### Recurring / Subscription Transaction with Predefined Schedule

<br />

Note that the [Recurring / Subscription mentioned above](#recurring-subscription-card-transaction) is relying on your system/backend to schedule and trigger the recurring charges. Additionally, Midtrans also **support automatically charge recurring for you based on your specified schedule**.

Follow the same implementation as [mentioned above](#recurring-subscription-card-transaction), to the point your system retrieved the `saved_token_id`. Then you can proceed with [Core API's Recurring API feature here](https://api-docs.midtrans.com/#subscription-api). To specify the schedule of when Midtrans should charge recurringly to your customer.

<br />

> 📘 Note
>
> This feature requires special MID from acquiring bank, this utilize what bank usually call as "recurring MID". Which may means additional business agreement with the acquiring bank, you should consult Midtrans Activation team to activate this feature.

<br />

This method also support [GoPay payment method](/reference/gopay-tokenization). Please contact Midtrans Activation Team or your Sales Representative before using this feature.

<br />

***

<br />

## Routing Transactions to Specific Acquiring Bank

<br />

There are two ways to route transactions to specific acquiring bank, one is by specifying acquiring explicitly during create token step by adding `bank` parameter, another is to add a logic to determine which acquiring transactions will be routed to based on card properties, using the Bank Routing feature.

<br />

### Specify acquiring explicitly

<br />

You can specify the preferred *Acquiring Bank* for specific Snap transaction. Transaction fund will be routed to that specific acquiring bank. Consult Midtrans Activation team to get information about the availability of the *Acquiring Bank*.

> Specify the bank name inside the `bank` parameter.

Example of the JSON parameters used during [backend API request step](/docs/snap-snap-integration-guide#1-acquiring-transaction-token-on-backend) is given below.

<br />

```json JSON Parameters
{
  "transaction_details": {
    "order_id": "CustOrder-102",
    "gross_amount": 9000
  },
  "credit_card": {
    "secure": true,
    "bank": "bca"
  }
}
```

```curl Sample Charge API Request
curl -X POST \
  https://app.sandbox.midtrans.com/snap/v1/transactions \
  -H 'Accept: application/json'\
  -H 'Authorization: Basic U0ItTWlkLXNlcnZlci1UT3ExYTJBVnVpeWhoT2p2ZnMzVV7LZU87' \
  -H 'Content-Type: application/json' \
  -d '{
  "transaction_details": {
    "order_id": "CustOrder-102",
    "gross_amount": 9000
  },
  "credit_card": {
    "secure": true,
    "bank": "bca"
  }
}'
```

<br />

### Route based on card properties via Bank Routing feature

<br />

In create token request, you can also specify the routing logic based on card properties using Bank Routing feature. When specified, Snap will then route transactions to specified acquiring based on card BIN or card type. `bank_routing` must be set inside the `credit_card` parameter when creating a Snap token.

Example of the JSON parameters used during [backend API request step](/docs/snap-snap-integration-guide#1-acquiring-transaction-token-on-backend) is given below.

<br />

```json JSON Parameters
...
  "credit_card": {
    "bank_routing": {
      "bin": {
        "bni": ["48111111", "debit", "bca"],
        "bri": ["5", "cimb"]
      }
    },
    "bank": "mandiri"
  }
...
```

```Text Sample Charge API Request
curl -X POST \
  https://app.sandbox.midtrans.com/snap/v1/transactions \
  -H 'Accept: application/json'\
  -H 'Authorization: Basic U0ItTWlkLXNlcnZlci1UT3ExYTJBVnVpeWhoT2p2ZnMzVV7LZU87' \
  -H 'Content-Type: application/json' \
  -d '{
  "transaction_details": {
    "order_id": "CustOrder-102",
    "gross_amount": 9000
  },
  "credit_card": {
    "secure": true,
    "bank_routing": {
      "bin": {
        "bni": ["48111111", "debit", "bca"],
        "maybank": ["5", "bca"]
      }
    },
    "bank": "mandiri"
  }
}'
```

<br />

Under `bin` JSON parameter, merchant can specify the preferred routing logic with the following key-value JSON format:

<br />

| Key                                                                                                                                                                                                                                                                                               | Value                                                                                                  |
| :------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ | :----------------------------------------------------------------------------------------------------- |
| String of card payment **acquiring bank code**. Possible values: `bni`, `bri`, `bca`, `mandiri`, `cimb`. Make sure to only pass values of acquiring banks that are activated for your account - check in your business agreement on what acquiring banks are activated for your merchant account. | Array of string of `Card BIN` , `Card Type`, and/or `Card Issuing Bank`. Explained in the table below. |

| Array Value Kind  | Description                                                                                                                                                                                                                                                                                                                                                                                                                            | Type   |
| :---------------- | :------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | :----- |
| Card BIN          | The first 6-8 digit of credit card number. You can specify part of the BINs only e.g. "4", "481", Snap will them perform regex matching. For example, specifying "4" only will cause all Visa cards to be redirected to the specified acquiring.                                                                                                                                                                                       | String |
| Card type         | Possible values : `debit` or `credit`. Midtrans will determine card type based on internal BIN database.                                                                                                                                                                                                                                                                                                                               | String |
| Card Issuing bank | Issuing bank codes, applicable only for Indonesian card issuing bank that are recognized by Midtrans BIN database. Supported values include bri/bni/bca/cimb/mandiri/permata. Be aware however that there are some instances where bank code might not include the most updated BINs and that you agree to accept this risk if you choose to use it. If BIN accuracy is crucial, we highly recommend inputting the exact BINs instead. | String |

<br />

Format Summary:

> "acquiring-bank-code" : \[ Array of string of either `Card BIN` , `Card Type`, or `Card Issuing Bank` ]

You can specify key-value pair multiple time as needed, with that format. Check the provided JSON example above for a practical example.

<br />

> 📘 Note
>
> 1. If there are 2 valid acquiring bank codes specified for the same card issuing bank, then the acquiring bank will be taken from order of the bin object that you specify. E.g. if you specify the same Card BIN across multiple acquiring bank codes, the first acquiring bank code will be chosen.
> 2. If a customer pay with a card which BIN is not specified in the `bank_routing`, such payment will then be routed based on the specified acquiring bank within the `bank` param, or randomized if no acquiring are specified in any of the logic within the create token request parameter.
> 3. Ensure that specified acquiring bank code is active for your merchant account, otherwise transaction may fail.

<br />

In case there are some conflicting logics defined within the request that includes `bank_routing`, Snap will prioritize routing based on the following hierarchy :

1. Whitelist/Blacklist bin
2. Online installment routing logic (as specified in the `installment` param)
3. **`bank_routing`param**
4. `bank` param specified inside `credit_card` object

<br />

***

<br />

## BIN Filter

<br />

BIN (Bank Identification Number) filter is a feature that allows the merchant to accept only Credit Cards within specific set of BIN numbers. It is useful for certain bank promo/discount payment by accepting only credit cards issued by that bank. BIN (Bank Identification Number) is the **first 1-8 digits of a card number**, which identifies the bank that issues the card. A bank generally has more than one BIN.

To use this feature, you need to accumulate the list of BIN that accepts the promotion or uses the issuing bank's name. This list of BIN or issuing bank name will then become a transaction parameter `whitelist_bins`. This transaction can only be performed exclusively by using the credit card that is included in the BIN list or BIN under the particular defined issuing bank.

Similarly, if you want to prevent a transaction from using a certain BIN properties instead, change the parameter name into `blacklist_bins`.

Example of the JSON parameters used during [backend API request step](/docs/snap-snap-integration-guide#1-acquiring-transaction-token-on-backend) is given below.

<br />

```json JSON Parameters
{
  "transaction_details": {
    "order_id": "CustOrder-102",
    "gross_amount": 120000
  },
  "credit_card": {
    "secure": true,
    "whitelist_bins": [
      "48111111",
      "41111111",
      "bni",
      "3"
    ],
    "blacklist_bins": [
      "5"
    ]
  }
}
```

```curl Sample Charge API Request
curl -X POST \
  https://app.sandbox.midtrans.com/snap/v1/transactions \
  -H 'Accept: application/json'\
  -H 'Authorization: Basic U0ItTWlkLXNlcnZlci1UT3ExYTJBVnVpeWhoT2p2ZnMzVV7LZU87' \
  -H 'Content-Type: application/json' \
  -d '{
  "transaction_details": {
    "order_id": "CustOrder-102",
    "gross_amount": 120000
  },
  "credit_card": {
    "secure": true,
    "whitelist_bins": [
      "48111111",
      "41111111",
      "bni"
    ]
  }
}'
```

<br />

Accepted BIN values:

<br />

| Array Value Kind  | Description                                                                                                                                                                                                                                                                                                                                                                                                                            | Type   |
| :---------------- | :------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | :----- |
| Card BIN          | The first 6-8 digit of credit card number. You can specify part of the BINs only e.g. "4", "481", Snap will them perform regex matching. For example, specifying "4" only will cause all Visa cards to be redirected to the specified acquiring.                                                                                                                                                                                       | String |
| Card Issuing bank | Issuing bank codes, applicable only for Indonesian card issuing bank that are recognized by Midtrans BIN database. Supported values include bri/bni/bca/cimb/mandiri/permata. Be aware however that there are some instances where bank code might not include the most updated BINs and that you agree to accept this risk if you choose to use it. If BIN accuracy is crucial, we highly recommend inputting the exact BINs instead. | String |

<br />

The sample Snap payment page with BIN Filter feature is displayed below.

<br />

![](https://files.readme.io/939caf4-snap-adv-bin-filter.png "snap-adv-bin-filter.png")

<br />

***

<br />

## Card Installment Payment

<br />

### Online Installment

<br />

This is the type of installment where the Card Issuer and Acquiring Bank is the same entity. For example, if a customer makes an installment payment using BNI Card, then *Acquiring Bank* is also BNI.

For online installments, the bank will issue special MID for installment. This installment MID is used to automatically convert the transaction into installments. To activate the installment feature, you are required to have agreement with the bank. Please consult your Midtrans's Sales PIC or Support team for installment MID. If installment MID is available, merchant simply needs to add the `installment` parameter.

<br />

```json
...
  "credit_card": {
    "secure": true,
    "installment": {
      // set to `true` to force customer pay with installment for this transaction
      // set to `false` to add option for customer to pay with regular fullpayment
      "required": true,
      "terms": {
        // input the desired bank & installment terms
        "<bank-name>": [ <installment terms as array of integers> ]
      }
    }
  }
...
```

Example of the JSON parameters used during [backend API request step](/docs/snap-snap-integration-guide#1-acquiring-transaction-token-on-backend) is given below.

<br />

```json JSON Parameters
{
  "transaction_details": {
    "order_id": "CustOrder-102",
    "gross_amount": 120000
  },
  "credit_card": {
    "secure": true,
    "installment": {
      "required": true,
      "terms": {
        "bca": [3,6,12],
        "bni": [3,6,12],
        "mandiri": [3,6,12],
        "cimb": [3,6,12],
        "bri": [3,6,12]
      }
    }
  }
}
```

```curl Sample Charge API Request
curl -X POST \
  https://app.sandbox.midtrans.com/snap/v1/transactions \
  -H 'Accept: application/json'\
  -H 'Authorization: Basic U0ItTWlkLXNlcnZlci1UT3ExYTJBVnVpeWhoT2p2ZnMzVV7LZU87' \
  -H 'Content-Type: application/json' \
  -d '{
  "transaction_details": {
    "order_id": "CustOrder-102",
    "gross_amount": 120000
  },
  "credit_card": {
    "secure": true,
    "installment": {
      "required": true,
      "terms": {
        "bca": [6,12],
        "bni": [6,12],
        "mandiri": [3,6,12]
      }
    }
  }
}'
```

The sample Snap payment page with online installment feature is displayed below.

<br />

![](https://files.readme.io/f36194c-snap-adv-online-installment.png "snap-adv-online-installment.png")

<br />

> 📘 Note
>
> Installment term will show in the Snap payment page after the customer enters the installment-compatible credit card number. For testing this method, refer to [Sandbox test card](/docs/testing-payment-on-sandbox#card-number).

<br />

### Offline Installment

<br />

*Offline Installment* is the type of payment where *Card Issuing Bank* used for making an installment payment and the *Acquiring Bank* need not be the same. For example, a customer makes an installment payment using BNI Card and the *Acquiring Bank* is Mandiri.

To allow installment feature with banks which do not issue Installment MID, merchant can use offline installment feature. With offline installment feature, the transaction will initially be charged in full amount and will be converted into installment later. To activate the installment feature, you are required to have agreement with the bank. Please consult Midtrans Activation Team for installment MID.

To use offline installment, you will need to add a param under installment object `offline_bins` and `terms` where you can specify the list of BINs that qualify for offline installment. Once you have set the BINs, Snap UI will automatically check if the card number inputted by customers is eligible for offline installment, if yes - Snap will then show the available terms for customer. Note that you can also configure this via [dashboard](/docs/snap-advanced-feature#configuring-installment-via-snap-preference) for no code alternative.

If you want to enforce the transaction to use offline installment however, you can  add the `installment` parameter with combination of `whitelist_bins` . The purpose of Whitelist BIN is to allow only certain acceptable cards to proceed with offline installment payment, based on the agreement between you and issuing banks.

Usually you will also need to add `bank` parameter to specify which card acquirer bank should be used for the offline installment payment, or you can also use the [bank routing feature](/docs/snap-advanced-feature#route-based-on-card-properties-via-bank-routing-feature) if you need to specify a custom bank routing logic based on BINs.

<br />

```json Using offline_bins
{
    "credit_card": {
        "secure": true,
        "installment": {
            // set to `true` to force customer pay with installment for this transaction
            // set to `false` to add option for customer to pay with regular fullpayment
            "required": true,
            "offline_bins": [<card BINs as array of strings>],
            "terms": {
                // input the desired installment terms
                "offline": [ <installment terms as array of integers>]
            }
        },
        "bank": <specify acquirer bank> // input the destination card acquirer bank that will be used
    }
}
```

```json Using Whitelist BIN
...
  "credit_card": {
    "secure": true,
    "installment": {
      // set to `true` to force customer pay with installment for this transaction
      // set to `false` to add option for customer to pay with regular fullpayment
      "required": true,
      "terms": {
        // input the desired installment terms
        "offline": [ <installment terms as array of integers> ]
      }
    },
    "whitelist_bins": [ <card BINs as array of strings> ],
    "bank": <specify acquirer bank> // input the destination card acquirer bank that will be used
  }
...
```

Example of the JSON parameters used during [backend API request step](/docs/snap-snap-integration-guide#1-acquiring-transaction-token-on-backend) is given below.

<br />

```json JSON Param - offline_bins
{
  "transaction_details": {
    "order_id": "CustOrder-102",
    "gross_amount": 120000
  },
  "credit_card": {
    "secure": true,
    "installment": {
      "required": true,
      "terms": {
        "offline": [3,6,12]
      }
    },
   	  "offline_bins": [
      "481111",
      "410505"
    ],
    "bank": "mandiri"
  }
}
```

```json JSON Param - Whitelist BINs
{
  "transaction_details": {
    "order_id": "CustOrder-102",
    "gross_amount": 120000
  },
  "credit_card": {
    "secure": true,
    "installment": {
      "required": true,
      "terms": {
        "offline": [3,6,12]
      }
    },
    "whitelist_bins": [
      "481111",
      "410505"
    ],
    "bank": "mandiri"
  }
}
```

```json Sample Charge API Request - offline_bins
curl -X POST \
  https://app.sandbox.midtrans.com/snap/v1/transactions \
  -H 'Accept: application/json'\
  -H 'Authorization: Basic U0ItTWlkLXNlcnZlci1UT3ExYTJBVnVpeWhoT2p2ZnMzVV7LZU87' \
  -H 'Content-Type: application/json' \
  -d '{
  "transaction_details": {
    "order_id": "CustOrder-102",
    "gross_amount": 120000
  },
  "credit_card": {
    "secure": true,
    "installment": {
      "required": true,
      "terms": {
        "offline": [3,6,12]
      }
    },
      "offline_bins": [
      "481111",
      "410505"
    ],
    "bank": "mandiri"
  }
}'
```

```curl Sample Charge API Request - Whitelist Bins
curl -X POST \
  https://app.sandbox.midtrans.com/snap/v1/transactions \
  -H 'Accept: application/json'\
  -H 'Authorization: Basic U0ItTWlkLXNlcnZlci1UT3ExYTJBVnVpeWhoT2p2ZnMzVV7LZU87' \
  -H 'Content-Type: application/json' \
  -d '{
  "transaction_details": {
    "order_id": "CustOrder-102",
    "gross_amount": 120000
  },
  "credit_card": {
    "secure": true,
    "installment": {
      "required": true,
      "terms": {
        "offline": [3,6,12]
      }
    },
    "whitelist_bins": [
      "481111",
      "410505"
    ],
    "bank": "mandiri"
  }
}'
```

<br />

The sample Snap payment page with offline installment feature is displayed below.

<br />

![](https://files.readme.io/b01ddc3-snap-adv-offline-installment.png "snap-adv-offline-installment.png")

<br />

### Definition

<br />

| Parameter       | Description                                                                                                                                                                                                                                                                                                                                                                                                                                 |
| --------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `required`      | If `true`, the customer must pay as installment for that specific transaction. <br />If `false`, the customer can choose to pay as installment or regular full payment for that specific transaction.                                                                                                                                                                                                                                       |
| `terms`         | under `terms` array, on online installment, you can specify the bank name <br />(For example- BNI, BCA, CIMB, Mandiri, and so on)                                                                                                                                                                                                                                                                                                           |
| offline\_bins   | Define the qualified card BINs (bank identification number - first 6-8 digits of a card number) to use offline installment. If qualified, the option to choose using offline installment (in form of terms selection) will be available for users. Accepted BIN format include Card BIN numbers or Issuing bank code (see this [section ](/docs/snap-advanced-feature#route-based-on-card-properties-via-bank-routing-feature)for examples. |
| whitelist\_bins | Define the qualified card BINs (bank identification number - first 6-8 digits of a card number) to to enforce trx to use cards with BINs as specified here. Accepted BIN format include Card BIN numbers or Issuing bank code (see this [section ](/docs/snap-advanced-feature#route-based-on-card-properties-via-bank-routing-feature)for examples.                                                                                        |

<br />

> ❗️
>
> When you use `whitelist_bins` to create a Snap transaction, it will be applied to all card transaction for that Snap transaction. It could means that you will not be able to mix offline installment with online installment or regular full payment into one Snap transaction. Read [further recommendations here](/docs/technical-faq#how-should-i-implement-offline-installment-card-payment).

<br />

### Add minimum amount to use installment validation

<br />

You can also specify the minimum amount to use installment - if customer checks out with basket size lower than the specified minimum amount, Snap will not show the installment options to customer. You can also configure this minimum amount settings via Snap Preference in your dashboard.

To use, add the param `minimum_amount` under the `credit_card` object and specify the minimum amount for each online installment bank or for offline installments.

<br />

```json
{
  "transaction_details": {
    "order_id": "ORDER-101",
    "gross_amount": 10000
  },
  "credit_card": {
    "installment": {
      "required": false,
      "terms": {
        "bni": [3, 6, 12],
      },
      "minimum_amount": {
        "bni": 100000,
			  "offline" : 50000
      }
    }
  }
}
```

<br />

### Configuring Installment via Snap Preference

<br />

You can also configure Installment settings via Snap Preference - the tab 'Credit Card Payment Settings' will appear in your Snap Preference if you have card payment method active in your Midtrans account. To activate card payment installment feature, contact your Sales PIC first or Midtrans support team for the required procedure.

Once card payment installment feature is activated, you can specify the config either via API request or via Snap Preference.

<br />

<Image alt="Installment Settings in Snap Preference" align="center" src="https://files.readme.io/7f57457-Installment_Config.png">
  Installment Settings in Snap Preference
</Image>

<br />

You can configure various Installment settings here, including :

| Feature                                   | Description                                                                                                                                                                                                                                                                                                                                                                                                                                 | Required |
| :---------------------------------------- | :------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ | :------- |
| Require customers to use installment      | If toggled active, Snap will force customers to use Installment in order to be able to proceed paying in Snap page. If toggled inactive, customer can still opt to pay using full payment.                                                                                                                                                                                                                                                  | Optional |
| Online/Offline installment toggle         | Toggle active in order to set up various settings pertaining to online/offline installment, e.g. terms and minimum amount                                                                                                                                                                                                                                                                                                                   | Required |
| Bank                                      | Check to activate which online installment to support. Banks shown up in here are based on what online installment bank MID are activated in your account.                                                                                                                                                                                                                                                                                  | Required |
| Terms                                     | Check to enable the possible installment terms that you want to offer to customer - this will affect the installment terms dropdown options shown in Snap. Terms shown up in here are based on what terms configured to your bank installment MID activated in your account.                                                                                                                                                                | Required |
| Minimum amount                            | Set the minimum amount for your customer to qualify to use installment. If the final amount prior to charge is lower than the configured minimum amount, customer will not be able to proceed to pay with installment.                                                                                                                                                                                                                      | Optional |
| BIN validation (offline installment only) | Define the qualified card BINs (bank identification number - first 6-8 digits of a card number) to use offline installment. If qualified, the option to choose using offline installment (in form of terms selection) will be available for users. Accepted BIN format include Card BIN numbers or Issuing bank code (see this [section ](/docs/snap-advanced-feature#route-based-on-card-properties-via-bank-routing-feature)for examples. | Optional |

<br />

> 📘
>
> * Do note that it might take up to 5 minutes to the configuration setup here to reflect in your Snap checkout page.\* If you specify installment config via both Snap Preference and when creating Snap Token, specification passed when creating Snap Token will be prioritized.

<br />

***

<br />

## Pre-Authorization Payment

<br />

Pre-authorization feature means customer's fund will not be directly deducted after transaction, but its amount/limit will be temporary reserved (blocked). Then you can initiate "capture" action later via [Core API](https://api-docs.midtrans.com/#capture-transaction). By default, if there is no "capture" action for the transaction for 7 days, reserved fund will be released after 7 days.

To use this feature, you need to add `"type": "authorize"` parameter.

Example of the JSON parameters used during [backend API request step](/docs/snap-snap-integration-guide#1-acquiring-transaction-token-on-backend) is given below.

<br />

```json JSON Parameters
{
  "transaction_details": {
    "order_id": "CustOrder-102",
    "gross_amount": 9000
  },
  "credit_card": {
    "secure": true,
    "type": "authorize"
  }
}
```

```curl Sample Charge API Request
curl -X POST \
  https://app.sandbox.midtrans.com/snap/v1/transactions \
  -H 'Accept: application/json'\
  -H 'Authorization: Basic U0ItTWlkLXNlcnZlci1UT3ExYTJBVnVpeWhoT2p2ZnMzVV7LZU87' \
  -H 'Content-Type: application/json' \
  -d '{
  "transaction_details": {
    "order_id": "CustOrder-102",
    "gross_amount": 9000
  },
  "credit_card": {
    "secure": true,
    "type": "authorize"
  }
}'
```

<br />

***

<br />

# <b>E-Wallet</b>

<br />

## Redirect Customer From Gojek App (GoPay)

<br />

After completing payment using GoPay payment method, by default, the customer will remain on Gojek app. They need to manually close Gojek app to switch back to merchant website or application. The `gopay.callback_url` parameter will allow customers to be automatically redirected from the Gojek app to the merchant website/application.

Example of the JSON parameters used during [backend API request step](/docs/snap-snap-integration-guide#1-acquiring-transaction-token-on-backend) is given below.

<br />

```json JSON Parameters
{
  "transaction_details": {
    "order_id": "CustOrder-102",
    "gross_amount": 9000
  },
  "gopay": {
    "enable_callback": true,
    "callback_url": "https://tokoecommerce.com/gopay_finish"
  }
}
```

```curl Sample Charge API Request
curl -X POST \
  https://app.sandbox.midtrans.com/snap/v1/transactions \
  -H 'Accept: application/json'\
  -H 'Authorization: Basic U0ItTWlkLXNlcnZlci1UT3ExYTJBVnVpeWhoT2p2ZnMzVV7LZU87' \
  -H 'Content-Type: application/json' \
  -d '{
  "transaction_details": {
    "order_id": "CustOrder-102",
    "gross_amount": 9000
  },
  "gopay": {
    "enable_callback": true,
    "callback_url": "https://tokoecommerce.com/gopay_finish"
  }
}'
```

You can input `callback_url` value with http/https URL protocol for website, or deeplink protocol for mobile App. For example, you can specify deeplink to your app: `"callback_url": "tokoecommerce://gopay_finish/"`

<br />

> 📘
>
> The final redirect URL will be appended with query parameter like `?order_id=xxx&result=xxx`.

<br />

For example the final redirect URL might look like this:

<br />

```
https://tokoecommerce.com/gopay_finish/?order_id=CustOrder-102123123&result=success
```

| Query Parameter | Description                                                                                                         | Type   |
| --------------- | ------------------------------------------------------------------------------------------------------------------- | ------ |
| order\_id       | Order ID sent on the Charge Request.                                                                                | String |
| result          | Result of the transaction to decide what kind of page to show to customer. Possible values: `success` or `failure`. | String |

<br />

> 📘 Note
>
> `gopay.callback_url` will only affect customer who pays with deeplink mode. Customer who pays with QR scan mode, will be redirected to Snap finish redirect URL, which you can [specify here](/docs/snap-advanced-feature#custom-finish-url).

<br />

You could utilize those information to display custom message to your customer on your finish URL.

<br />

***

<br />

## Force Deeplink/QRIS Regardless of Screensize

<br />

Snap payment screen by default will automatically detect customer device being used for transaction. If the device is detected as a mobile device, Snap will use deeplink mode to redirect customer to Gojek/Shopeepay app (if installed on the device). If the device is detected as a non mobile device, Snap will use QR mode to display QR Code for customer to be paid from their mobile device.

You can specify options `uiMode` on Snap.js to force Snap to use QR or deeplink as specified.

<br />

| Option | Description                                                                                                              | Type   | Required |
| ------ | ------------------------------------------------------------------------------------------------------------------------ | ------ | -------- |
| uiMode | Choose the UI mode for GoPay/Shopeepay. <br />Supported values are `deeplink`, `qr`, and `auto`. Set to auto by default. | String | Optional |

<br />

Example of the Snap.js callback option usage (this parameter is used during [Snap frontend implementation](/docs/snap-snap-integration-guide#2-displaying-snap-payment-page-on-frontend)), while calling `window.snap.pay(...)` is given below.

<br />

```javascript Frontend JS
window.snap.pay('SNAP_TRANSACTION_TOKEN', {
  uiMode: "deeplink"
})
```

<br />

### Snap Redirect Mode

<br />

If you are using Snap Redirect Mode, you can append options as Query parameter `?gopayMode=deeplink` at the end of the Snap `redirect_url`, which for example the final result url as shown below.

<br />

```
https://app.sandbox.midtrans.com/snap/v2/vtweb/cf9534e3-ddf7-43f9-a1b7-5f618d2d1c96?gopayMode=deeplink
```

For more details, refer to [GoPay](/reference/gopay).

<br />

***

<br />

## GoPay Account Linking / Tokenization

<br />

Snap also supports payment with GoPay where your customer can account their GoPay account to merchant's Snap payment page - subsequent payment can then be made without having to redirect customer to GoPay app.

You will need to request for this feature activation via [Support](https://midtrans.com/contact-us), or reach out to your Midtrans's PIC.

For further details on how to use this feature, check out the API references [here](/reference/gopay-tokenization-1).

<br />

***

# <b>Bank Transfer / VA</b>

<br />

## Specify VA Number

<br />

By default Midtrans will randomize VA number used for bank transfer transaction. In some cases, you might want to specify/customize VA Number for Bank Transfer payment channels. You can do that with the following parameters.

Example of the JSON parameter used during [backend API request step](/docs/snap-snap-integration-guide#1-acquiring-transaction-token-on-backend) is given below.

<br />

```json JSON Paramete
{
  "transaction_details": {
    "order_id": "CustOrder-102",
    "gross_amount": 9000
  },
  "bca_va": {
    "va_number": "12345678901",
    "sub_company_code": "00000" //NOTE: Don't send this field unless BCA give you sub company code
  },
  "bni_va": {
    "va_number": "12345678"
  },
  "bri_va": {
    "va_number": "12345678"
  },
  "permata_va": {
    "va_number": "1234567890"
  }
}
```

```curl Sample Charge API Request
curl -X POST \
  https://app.sandbox.midtrans.com/snap/v1/transactions \
  -H 'Accept: application/json'\
  -H 'Authorization: Basic U0ItTWlkLXNlcnZlci1UT3ExYTJBVnVpeWhoT2p2ZnMzVV7LZU87' \
  -H 'Content-Type: application/json' \
  -d '{
  "transaction_details": {
    "order_id": "CustOrder-102",
    "gross_amount": 9000
  },
  "bca_va": {
    "va_number": "12345678901",
    "sub_company_code": "00000"
  },
  "bni_va": {
    "va_number": "12345678"
  },
  "bri_va": {
    "va_number": "12345678"
  },
  "permata_va": {
    "va_number": "1234567890"
  }
}'
```

Virtual Account number displayed to customer contains two parts. for example, in `{91012}{12435678}` , the company-prefix-number and the second part is a unique-VA-number. The second part is the part that can be customized.

<br />

> 📘
>
> * Only digits are allowed.\* Different banks have different specifications on their custom VA numbers. Please see the documentation of the respective banks. Note: for **Permata, only B2B VA type** support custom VA numbers. *If the number provided is already utilized for another order, then a different unique number will be used instead.* If the number provided is longer than required, then the unnecessary digits in the end will be trimmed.\* If the number provided is shorter than required, then the number will be prefixed with zeros.

<br />

| Parameter              | Description                                                                                      | Type   | Required |
| ---------------------- | ------------------------------------------------------------------------------------------------ | ------ | -------- |
| BCA `va_number`        | Length should be within 1 to 11.                                                                 | String | Optional |
| BCA `sub_company_code` | BCA sub company code directed for this transactions. <br />NOTE: Don't use it if you don't know. | String | Optional |
| Permata `va_number`    | Length should be 10. Only supported for B2B VA type.                                             | String | Optional |
| BNI `va_number`        | Length should be within 1 to 8.                                                                  | String | Optional |

<br />

> 📘 Note
>
> In *Production* mode, your VA payment method might not by default support custom VA number as some might require additional agreement with bank partners . Please consult your Midtrans's Sales PIC or Support team for further information.

<br />

***

<br />

## Specify VA Description

<br />

Some VA description and recipient name can be customized.

Example of the JSON param for the first or initial transaction is given below. This param is used during [backend API request step](/docs/snap-snap-integration-guide#1-acquiring-transaction-token-on-backend).

<br />

```json JSON Parameters
{
  "transaction_details": {
    "order_id": "CustOrder-102",
    "gross_amount": 9000
  },
  "bca_va": {
    "free_text": {
      "inquiry": [
        {
          "en": "Invoice for Order 3123 - Toy Shop",
          "id": "Tagihan untuk Order 3123 - Toy Shop"
        }
      ],
      "payment": [
        {
          "en": "Pay Order 3123 - Toy Shop",
          "id": "Bayar Order 3123 - Toy Shop"
        }
      ]
    }
  },
  "permata_va": {
    "recipient_name": "Budi Susanto"
  }
}
```

```curl Sample Charge API Request
curl -X POST \
  https://app.sandbox.midtrans.com/snap/v1/transactions \
  -H 'Accept: application/json'\
  -H 'Authorization: Basic U0ItTWlkLXNlcnZlci1UT3ExYTJBVnVpeWhoT2p2ZnMzVV7LZU87' \
  -H 'Content-Type: application/json' \
  -d '{
  "transaction_details": {
  "transaction_details": {
    "order_id": "CustOrder-102",
    "gross_amount": 9000
  },
  "bca_va": {
    "free_text": {
      "inquiry": [
        {
          "en": "Invoice for Order 3123 - Toy Shop",
          "id": "Tagihan untuk Order 3123 - Toy Shop"
        }
      ],
      "payment": [
        {
          "en": "Pay Order 3123 - Toy Shop",
          "id": "Bayar Order 3123 - Toy Shop"
        }
      ]
    }
  },
  "permata_va": {
    "recipient_name": "Budi Susanto"
  }
}'
```

**BCA Virtual Account**

<br />

* **Inquiry** free text is list of text that will be displayed on ATM (if supported) when customer attempts to check/enquire the VA number.
* **Payment** free text is list of text that will be displayed on ATM (if supported) when customer attempts to pay the VA number.

<br />

BCA VA Free Text Array:

| Parameter | Description              | Type                  | Required |
| --------- | ------------------------ | --------------------- | -------- |
| inquiry   | Max item for array is 10 | Array of FreeTextItem | Optional |
| payment   | Max item for array is 10 | Array of FreeTextItem | Optional |

<br />

BCA VA Free Text Item:

| Parameter | Description                           | Type   | Required |
| --------- | ------------------------------------- | ------ | -------- |
| en        | Size should not exceed 50 characters. | String | Required |
| id        | Size should not exceed 50 characters. | String | Required |

<br />

**Permata Virtual Account**

<Table>
  <thead>
    <tr>
      <th>
        Parameter
      </th>

      <th>
        Description
      </th>

      <th>
        Type
      </th>

      <th>
        Required
      </th>
    </tr>
  </thead>

  <tbody>
    <tr>
      <td>
        recipient\_name
      </td>

      <td>
        Recipient name shown on the on the bank’s payment prompt. <br />It is shown as 20 character uppercase string. <br />Anything over 20 characters will be truncated. NOTE: Default is merchant name.
      </td>

      <td>
        String
      </td>

      <td>
        Optional
      </td>
    </tr>
  </tbody>
</Table>

<br />

***

<br />

# <b>Convenience Store</b>

<br />

## Specify Alfamart Free Text

<br />

The text printed on Alfamart receipt can be customized.

Example of the JSON parameters used during [backend API request step](/docs/snap-snap-integration-guide#1-acquiring-transaction-token-on-backend) is given below.

<br />

```json JSON Parameters
{
  "transaction_details": {
    "order_id": "CustOrder-102",
    "gross_amount": 9000
  },
  "cstore": {
    "alfamart_free_text_1" : "Thanks for shopping with Toy Store!",
    "alfamart_free_text_2" : "Visit our site at toystore.com",
    "alfamart_free_text_3" : "Invite your friend and get discount."
  }
}
```

```curl Sample Charge API Request
curl -X POST \
  https://app.sandbox.midtrans.com/snap/v1/transactions \
  -H 'Accept: application/json'\
  -H 'Authorization: Basic U0ItTWlkLXNlcnZlci1UT3ExYTJBVnVpeWhoT2p2ZnMzVV7LZU87' \
  -H 'Content-Type: application/json' \
  -d '{
  "transaction_details": {
    "order_id": "CustOrder-102",
    "gross_amount": 9000
  },
  "cstore": {
    "alfamart_free_text_1" : "Thanks for shopping with Toy Store!",
    "alfamart_free_text_2" : "Visit our site at toystore.com",
    "alfamart_free_text_3" : "Invite your friend and get discount."
  }
}'
```

| Parameter               | Description                               | Type       | Required |
| ----------------------- | ----------------------------------------- | ---------- | -------- |
| alfamart\_free\_text\_1 | First row of printed receipt description  | String(40) | Optional |
| alfamart\_free\_text\_2 | Second row of printed receipt description | String(40) | Optional |
| alfamart\_free\_text\_3 | Third row of printed receipt description  | String(40) | Optional |

<br />

***

<br />

# <b>Consideration and Limitation</b>

There are a few limitations to consider while using Midtrans API. These limitations are given below.

<br />

### Maximum Request Size Limit

Midtrans API allows maximum size of **16kb** per request (**\~16000 total characters**). Please try to keep it under the limit to avoid request failures.

<br />

> 📘 Tip
>
> Limit the number of `item_details` from your request, or group it into fewer `item_details`.

<br />

### Snap Token Expiry Time

The default lifetime for regular Snap transaction, Snap `token` and also the `redirect_url`, is **24 hours**. It can be customized. To customize Snap `token` and `redirect_url`, refer to [Custom Expiry](#custom-transaction-expiry) section.

Within the set time limit, the payment page is available for customer to proceed payment. After the limit exceeds, it will show that the payment page is no longer available.

<br />

### Allowing Customer to Re-pay The Same Order

If your customer journey allow customer to close the payment page, and you want to allow customer to retry payment for that same order, you can follow this tips. When you sent request to Snap API and received Snap Token, you can keep/store that Snap Token associated with your Order ID. You can re-call `window.snap.pay(SNAP_TRANSACTION_TOKEN')` again on frontend with that same Snap Token to open the payment page popup, as [long as it is not expired](#snap-token-expiry-time).

Alternatively if you prefer to create another Snap Token, you can [follow the next section.](#re-creating-snap-token-for-particular-order)

<br />

### Re-creating Snap Token for Particular Order

At the state of customer have not chosen/proceed with any payment method within the Snap payment page, you can still re-initiate create Snap transaction using the *same Order ID*, since the Order ID is not yet utilized. New Snap token & url will be generated, and the older token & url will no longer be valid.

But that will no longer work if the customer has proceeded to choose/confirm to pay with certain payment methods, the status may have changed to `pending`. This also applies to when the customer has successfully paid the transaction. You will get API error message `transaction_details.order_id sudah digunakan`. This is to prevent duplication of payment order id on Midtrans side.

To avoid Order ID duplication, you can also change your implementation logic to allow one Order ID on your system to have-many payment Order ID on Midtrans (one-to-many relationship). e.g. for Order ID: `web-order-321` your system can send request to Snap API with timestamp suffix on Order ID param like `web-order-321-{$timestamp}`. So that you can have many Snap Token recreated for that one particular order.

<br />

### Note on Card Transaction Expire Notification

Card transaction payment status will become `pending` once it is proceeded into 3DS/OTP phase. If the transaction is abandoned or not completed within 10-15 minutes, its status will become `expire`. For more details [please refer here](/reference/status-code-2xx).

<br />

### Multiple Payment Attempts

Snap is designed to maximize conversion rate of customer payment, it has built-in behavior:

* In which **1 Snap order-id is allowed to be retried multiple times** by the customer as long as it is not yet finally paid or expired.

Example scenario:

* Customer attempted to pay a Snap Order ID using card payment, then his card declined 2x.
* Then he choose another payment method, Akulaku, in which he got 1x another declined payment.
* Then finally he chose another payment method, Gopay, in which his payment is successfully accepted.
* In summary: 3x deny attempts, 1x final success attempt.

Thus may result in:

* On **Midtrans dashboard you may see for 1 Snap Order ID, it can have multiple attempts recorded**.
  * In the example scenario above, you will see 2x denied card transactions + 1x denied Akulaku transactions + finally 1x successful Gopay transactions. This is **normal as long as there are only 1 successful transaction** for 1 Order ID. It is expected that the multiple failed transactions are also recorded.
* Your backend/system may **receive multiple webhook/HTTP Notifications** of transaction status for 1 Snap order ID, according to the status of each unique attempts.
  * In the example scenario above, your system will get 2x denied card transactions notif + 1x denied Akulaku transactions notif + finally 1x successful Gopay transactions notif. This is **normal as long as there are only 1 successful transaction** for 1 Order ID. It is expected that the multiple failed transaction notifications are also triggered.

<br />

> 📘 Tips
>
> You should implement your backend/system **to allow early failed payment attempts & eventually accept notification of payment success**.

<br />

### Retrieving e-Wallet & QRIS Deeplink/QR URL

For GoPay, ShopeePay, and QRIS transactions made within Snap payment UI, currently it's only possible to programmatically retrieve the `actions[].url` (of the payment deeplink/QR url) via [Core API](/reference/core-api-overview).

You can alternatively store the Snap payment `token` (or `redirect_url`), and use it to re-display the Snap payment UI to the customer, so the GoPay, ShopeePay and QRIS payment page will be re-displayed according to the customer's session.

<br />

### QRIS Related Behavior

This behavior is applicable to Snap as per changes introduced on 12 September 2022 in order to provide clearer information on the payment source.

Background condition:

* When you enable `gopay` (or other QRIS compatible e-Wallet methods like `shopeepay`) payment method on Snap, by default Snap UI (whenever possible) will **automatically decide and show two possible scenario** for customer to pay:
  * A) If the customer is detected using **mobile device, App Redirect** payment flow will be presented.
  * B) If the customer is detected using **Desktop/PC, QRIS Scan** payment flow will be presented.

Please note the following behavior:

* Depending on which method the customer pay with (A or B), for each payment attempts:
  * In **scenario A**: Midtrans will **mark the payment\_type of the transaction as** `gopay` (or the e-Wallet payment method chosen).
  * In **scenario B**: Midtrans will **mark the payment\_type of the transaction as** `qris`.
* This will also impact the following:
  * JSON fields sent on **Webhook/HTTP Notification** of the payment status.
  * **Get Status API** response.
  * The `Payment Type` displayed on **Midtrans Dashboard**, and when you download transactions.
* The JSON fields format (returned on Webhook/HTTP Notifications & Get Status API response) is different between `"payment_type": "qris"` and `"payment_type": "gopay"`. Refer to the [notification example section](/docs/https-notification-webhooks#sample-for-various-payment-methods) for details.

Compared to previous behavior before this change, it was:

* Regardless of scenario A or B, Midtrans will record it as `gopay` (or the e-Wallet payment method chosen).

**Recommended action for merchant** side:

* Please **ensure that your system’s implementation can handle this behavior** without breaking your system & its payment flow. Especially on the following parts (if applicable):
  * Your **Notifications Handler implementation** logic.
  * **Get Status API implementation** logic.
  * **Reconciliation implementation** logic.

<br />

> 📘 Tips
>
> * In terms of payment status, there will be no difference between the mentioned payment\_type. Success status will still be `settlement`, unpaid will still be `expire`, waiting will still be `pending`, etc. So it will be safe for you to use the same logic (no changes) to check the payment status.
> * It will also generally support the same command such as `refund`, `expire`, etc.
> * The change in `payment_type` will not affect payment status itself.
> * If it helps make your implementation transition easier, you can disregard the payment\_type differences or, internally consider both `gopay` and `qris` payment\_type to be one, just like previously.

<br />

### Note on Core API Get Status

When a transaction is created on *Snap API*, it does not immediately assign any payment status on *Core API's* Get Status response.

Considering this, you may get `404` or *Payment Not Found* response upon calling *Core API* get-status even if the payment page is activated on *Snap API*.

This condition may happen when customer may not have chosen/proceed with any payment method within the Snap payment page (For example, idling or abandoning the Snap payment page). After customer chooses and proceeds with a payment method, the transaction status will be assigned. It will be available on *Core API* Get Status response. The possible status is as defined in the table above.

<br />

### Content Security Policy (CSP) Whitelist

If you are using Content Security Policy (CSP) on your payment web page, please whitelist the domains/URLs given below. This is required by the Snap.js to work properly from within your payment web page.

```
cloudfront.net
*.midtrans.com
*.veritrans.co.id
*.mixpanel.com
*.google-analytics.com
```

Please whitelist the above domains/URLs in your CSP header/rule, to ensure proper working of Snap.js.

<br />

### Snap Popup in an IFrame

**Avoid** displaying Snap payment popup within an iframe from your main checkout page. As it may cause unexpected results (known so far):

* Snap UI size may not perfectly fit the size of the browser/device.
  * Snap automatically tries to fit to the webpage’s size, if you put Snap within an iframe, it tries to fit to the iframe size instead. Thus it is not recommended. If you insist on doing it with iframe, you can try to resize the iframe size to fit the main webpage’s.
* Payment methods that require redirect to payment provider's website/app may not work (e.g. Gopay, ShopeePay, etc.).
  * This is mainly because of Web Browser's security limitations, to avoid spam and unwanted pop-up most of modern web browser by default will try to block redirection coming from cross-domain iframe.

We always recommend to follow the recommended Snap integration:

* Use Snap’s javascript directly on your checkout webpage, and [make sure to use meta-viewport tag](/docs/snap-snap-integration-guide#2-displaying-snap-payment-page-on-frontend).
* Or alternatively use [Snap’s redirect method](/docs/snap-snap-integration-guide#alternative-way-to-display-snap-payment-page-via-redirect).