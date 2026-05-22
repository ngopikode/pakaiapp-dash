# Integration: Cardless Credit Payment

*Cardless Credit* is one of the payment methods offered by Midtrans. Using this payment method, the customers can shop online and make payments in installments. Midtrans sends real-time notifications to you when the customer completes the payment. Currently, Midtrans can integrate with Akulaku and Kredivo.

![](https://files.readme.io/fe8d23e-akulaku_logo.svg "akulaku_logo.svg")

<br />

<details>
  <summary><b>Sequence Diagram Transaction Flow</b></summary>

  <article>
    ![](https://files.readme.io/5790389-seq_cardless_credit.png "seq_cardless_credit.png")
  </article>
</details>

> 📘 Kredivo Integration Guide
>
> Guide coming soon for Kredivo. In the meantime, please refer to [Kredivo's Core API Reference](/reference/kredivo-1)

<br />

***

# <b>Sandbox Environment</b>

<br />

The steps given below uses Midtrans *Sandbox* environment to test the integration process. Please make sure that you use the *Server Key* and *Client Key* for the *Sandbox* environment. For more details, refer to [Retrieving API Access Keys](/docs/midtrans-account#retrieving-api-access-keys).

<br />

***

# <b>Steps for integration</b>

<br />

To integrate with *Cardless Credit* payment method, follow the steps given below.

<br />

## 1. Sending transaction data to Charge API

<br />

The *Charge API* request is sent with the transaction details, from the merchant backend.

<br />

### Request Details

| Environment | Method | URL                                                                                      |
| ----------- | ------ | ---------------------------------------------------------------------------------------- |
| Sandbox     | POST   | [https://api.sandbox.midtrans.com/v2/charge](https://api.sandbox.midtrans.com/v2/charge) |
| Production  | POST   | [https://api.midtrans.com/v2/charge](https://api.midtrans.com/v2/charge)                 |

<br />

### HTTP Headers

```text
Accept: application/json
Content-Type: application/json
Authorization: Basic AUTH_STRING
```

**AUTH\_STRING**: Base64Encode(`"YourServerKey"+":"`)

> 📘
>
> Midtrans API validates HTTP request by using Basic Authentication method. The username is your **Server Key** while the password is empty. The authorization header value is represented by AUTH\_STRING. AUTH\_STRING is base-64 encoded string of your username and password separated by colon symbol (**:**). For more details, refer to [ API Authorization and Headers](/docs/api-authorization-headers).

<br />

### Sample Request

The sample CURL request for *Charge API* for Akulaku *Cardless Credit* payment method, is shown below. You may implement according to your backend language. For more details, refer to available [Language Libraries](/docs/midtrans-api-libraries-plugins#language-library).

```curl Akulaku
curl -X POST \
  https://api.sandbox.midtrans.com/v2/charge \
  -H 'Accept: application/json' \
  -H 'Authorization: Basic <YOUR SERVER KEY ENCODED in Base64>' \
  -H 'Content-Type: application/json' \
  -d '{
  "payment_type": "akulaku",
  "transaction_details": {
      "order_id": "order-101",
      "gross_amount": 44000
  }
}'
```

<details>
  <summary>POST JSON Body Attribute Description for Akulaku</summary>

  <article>
    | Element              | Description                                                             | Type   | Required |
    | -------------------- | ----------------------------------------------------------------------- | ------ | -------- |
    | payment\_type        | Cardless Credit payment type.                                           | String | Required |
    | transaction\_details | The details of the transaction such as the order\_id and gross\_amount. | Object | Required |
    | order\_id            | The order\_id of the transaction.                                       | String | Required |
    | gross\_amount        | The total amount of transaction.                                        | Long   | Required |
  </article>
</details>

> 📘 Tips
>
> You can [include more information](/docs/coreapi-advanced-features#recommended-parameters) such as `customer_details`, `item_details`, and so on. It is recommended to send more details regarding the transaction, so that these details will be captured on the transaction record. Which can be [viewed on the Midtrans Dashboard](/docs/midtrans-dashboard-usage#transaction).

<br />

Learn more on why this API request [should be securely managed from your backend](/docs/payment-security#keep-sensitive-parameters-secured).

<br />

### Sample Response and Response Body

```json Akulaku
{
    "status_code": "201",
    "status_message": "Success, Akulaku transaction is created",
    "transaction_id": "fa05cba0-8ea3-4e46-a2b1-daea2a01785c",
    "order_id": "order-101-1578567480",
    "redirect_url": "https://api.sandbox.midtrans.com/v2/akulaku/redirect/fa05cba0-8ea3-4e46-a2b1-daea2a01785c",
    "merchant_id": "G812785002",
    "gross_amount": "11000.00",
    "currency": "IDR",
    "payment_type": "akulaku",
    "transaction_time": "2020-01-09 17:58:00",
    "transaction_status": "pending",
    "fraud_status": "accept"
}
```

<details>
  <summary>Response Body JSON Attribute Description for Akulaku</summary>

  <article>
    | Element             | Description                                                          | Type   | Notes                                                                                              |
    | ------------------- | -------------------------------------------------------------------- | ------ | -------------------------------------------------------------------------------------------------- |
    | status\_code        | This is the status of the API call.                                  | String | For more details, refer to [Error Code and Response Code](/docs/error-code-and-response-code).     |
    | status\_message     | A message describing the status of the transaction from Akulaku.     | String |                                                                                                    |
    | transaction\_id     | The *Transaction ID* of the specific transaction.                    | String |                                                                                                    |
    | order\_id           | The specific *Order ID*.                                             | String |                                                                                                    |
    | redirect\_url       | The URL to which the customer is redirected from the bank's website. | String |                                                                                                    |
    | merchant\_id        | Your merchant ID.                                                    | String |                                                                                                    |
    | gross\_amount       | The total amount of transaction for the specific order.              | String |                                                                                                    |
    | currency            | The unit of currency used for the transaction.                       | String |                                                                                                    |
    | payment\_type       | The type of payment method used by the customer for the transaction. | String |                                                                                                    |
    | transaction\_time   | The date and time at which the transaction occurred.                 | String | It is in the format, *YYYY-MM-DD* *HH:MM:SS.*<br />Time zone: Western Indonesian Time (GMT+7)      |
    | transaction\_status | The status of the transaction.                                       | String | For more details, refer to [Transaction Status](/docs/get-status-api-requests#transaction-status). |
    | fraud\_status       | The fraud status of the transaction.                                 | String | For more details, refer to [Fraud Status](/docs/get-status-api-requests#fraud-status).             |
  </article>
</details>

> 📘 Note
>
> The `redirect_url` attribute for the transaction is received.

<br />

### Status Codes and Errors

| Code | Description                            | Notes                                                     |
| ---- | -------------------------------------- | --------------------------------------------------------- |
| 201  | Successful transaction.                | –                                                         |
| 400  | The `transaction_details` are missing. | Make sure the `order_id` and `gross_amount` are included. |
| 413  | Syntax error.                          | Check the syntax.                                         |
| 500  | Internal system error occurred.        | You can try again later.                                  |

<br />

## 2. Redirecting customer to Cardless Credit Website

<br />

The `redirect_url` retrieved from the previous step is used to redirect the customer to the bank's website.\
You can redirect the customer through server-side redirect, using JavaScript like `window.location=[REDIRECT URL]`, or using HTML link `<a href="[REDIRECT URL]">Pay Here!</a>`.\
The customer can complete the payment on this page.

For more details, refer to [Testing Payment on Sandbox](/docs/testing-payment-on-sandbox#cardless-credit).

<br />

## 3. Configuring landing page

<br />

After the customer completes the payment, the bank's website redirects the customer to *Finish Redirect URL* which can be configured on Merchant Administration Portal (MAP).

<details>
  <summary><b>Configuring Finish Redirect URL</b></summary>

  <article>
    <br />

    To configure the *Finish Redirect URL*, follow the steps given below.

    1. Login to your MAP account.
    2. On the Home page, go to **SETTINGS > CONFIGURATION**.\
       *Configuration* page is displayed.
    3. Enter **Finish Redirect URL** with your landing page endpoint.
    4. Click **Update**. A confirmation message is displayed.

    ![](https://files.readme.io/8721c13-core-api-finish-redirect-url-2.png "core-api-finish-redirect-url-2.png")

    <br />

    The *Finish Redirect URL* is configured.
  </article>
</details>

Then, depending on which payment method, you should implement your Finish Redirect URL to handle the redirection:

### Akulaku Landing Page

> 📘 Note
>
> The redirection back from **Akulaku** to your Finish Redirect URL will be using **HTTP POST**.
>
> Please make sure the *Finish Redirect URL* endpoint can receive **HTTP POST** request.

The sample implementation code in *PHP* is given below. Please adjust the code to your own tech stack.

```php Sample Code
<?php
    // Read field `response` from the HTTP POST's Request Body.
    $response = $_POST['response'];
    // The value will be a String of JSON, so need to decode it into an object.
    $decoded_response = json_decode($response); 
    // Then you can access the fields within that object.
    $order_id = $decoded_response->order_id;
?>
```

```shell Sample Finish Redirect Request in CURL
# HTTP Request type: POST
curl 'https://<YOUR FINISH REDIRECT URL>' \
  -H 'content-type: application/x-www-form-urlencoded' \
  --data-raw 'response=%7B%22transaction_time%22%3A%222023-11-08+15%3A54%3A11%22%2C%22gross_amount%22%3A%2220000.00%22%2C%22currency%22%3A%22IDR%22%2C%22order_id%22%3A%22sample-store-1699433645%22%2C%22payment_type%22%3A%22akulaku%22%2C%22signature_key%22%3A%2201f8a501d52d87dfdbd2984009ed9740653fb79288ded6b3e50695308798682be113259529220d700571bb36afee4ed488c3769cc23275861863926efbc6b13e%22%2C%22status_code%22%3A%22200%22%2C%22transaction_id%22%3A%22e37e6f11-8f1f-436a-87fc-e3b938567d1d%22%2C%22transaction_status%22%3A%22settlement%22%2C%22fraud_status%22%3A%22accept%22%2C%22expiry_time%22%3A%222023-11-08+17%3A54%3A11%22%2C%22payment_channel%22%3A%22VTWEB%22%2C%22merchant_id%22%3A%22M007743%22%2C%22redirect_url%22%3A%22https%3A%2F%2FYourFinishRedirectUrl.com%2F%22%7D&callback_key=YourAPIClientKey'
  
# Decoded version of the HTTP Body
# response: 
# {
#    "transaction_time": "2023-11-08 15:54:11",
#    "gross_amount": "20000.00",
#    "currency": "IDR",
#    "order_id": "sample-store-1699433645",
#    "payment_type": "akulaku",
#    "signature_key": "01f8a501d52d87dfdbd2984009ed9740653fb79288ded6b3e50695308798682be113259529220d700571bb36afee4ed488c3769cc23275861863926efbc6b13e",
#    "status_code": "200",
#    "transaction_id": "e37e6f11-8f1f-436a-87fc-e3b938567d1d",
#    "transaction_status": "settlement",
#    "fraud_status": "accept",
#    "expiry_time": "2023-11-08 17:54:11",
#    "payment_channel": "VTWEB",
#    "merchant_id": "M007743",
#    "redirect_url": "https://YourFinishRedirectUrl.com"
# }
# callback_key: YourAPIClientKey
```

### Kredivo Landing Page

> 📘 Note
>
> The redirection back from **Kredivo** to your Finish Redirect URL will be using straight-forward **HTTP GET**.
>
> Please make sure the *Finish Redirect URL* endpoint can receive **HTTP GET** request.

## 4. Handling post-transaction

<br />

When the transaction status changes, Midtrans notifies you at the redirect URL and sends HTTP notification to the merchant backend. This ensures that you are updated of the transaction status securely.

HTTP POST request with JSON body will be sent to your *Payment Notification URL* configured on *Dashboard*.

<br />

<details>
  <summary><b>Configuring Payment Notification URL</b></summary>

  <article>
    <br />

    To configure the Payment Notification URL, follow the steps given below.

    1. Login to your MAP account.
    2. On the Home page, go to **SETTINGS > CONFIGURATION**.\
       *Configuration* page is displayed.
    3. Enter **Payment Notification URL**.
    4. Click **Update**. A confirmation message is displayed.

    ![](https://files.readme.io/f1f8719-core-api-payment-notification-1.png "core-api-payment-notification-1.png")

    <br />

    The *Payment Notification URL* is configured.
  </article>
</details>

<br />

The sample HTTP notification request received at merchant backend for Akulaku *Cardless Credit* payment method is given below.

```json Akulaku
{
  "transaction_time": "2020-01-09 17:58:00",
  "transaction_status": "settlement",
  "transaction_id": "fa05cba0-8ea3-4e46-a2b1-daea2a01785c",
  "status_message": "midtrans payment notification",
  "status_code": "200",
  "signature_key": "331d8619f0c53ce97abf4cfc91fae8d8d0b11da1640b5bb136b0cfbc0da161d50fc6d0dd0d7f893977881710a2d2c174d9e036aaaa772e80fdeac6e9fb60e6b9",
  "settlement_time": "2020-01-09 18:00:48",
  "payment_type": "akulaku",
  "order_id": "order-101-1578567480",
  "merchant_id": "G812785002",
  "gross_amount": "11000.00",
  "fraud_status": "accept",
  "currency": "IDR"
}
```

See also :  [HTTP(S) Notification/Webhooks](/docs/https-notification-webhooks)

<br />

***

# <b>Switching to Production Environment</b>

<br />

Follow the steps given below to switch to Midtrans *Production* environment and to accept real payments from real customers.

1. Change API domain URL from `api.sandbox.midtrans.com` to `api.midtrans.com`.
2. Use *Client Key* and *Server Key* for *Production* environment. For more details, refer to [Retrieving API Access Keys](/docs/midtrans-account#retrieving-api-access-keys).