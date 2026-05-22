# Integration: Over the Counter Payment

*Over the Counter* is one of the payment methods offered by Midtrans. Using this payment method, customers can shop online and make payments at a nearest convenience store. Midtrans sends you real-time notifications when the customer completes the payment. Currently, Midtrans has integrated with the convenience stores such as Alfamart group (Alfamart, Alfamidi, DAN+DAN) and Indomaret.

<Image title="alfamart_logo.svg" alt={3780} align="center" className="border" width="80%" border={true} src="https://files.readme.io/7a5247a-alfamart_logo.svg" />

<Image title="indomaret_logo.png" alt={3524} align="center" className="border" width="80%" border={true} src="https://files.readme.io/01a9595-indomaret_logo.png" />

<br />

<details>
  <summary><b>Sequence Diagram Transaction Flow</b></summary>

  <article>
    ![](https://files.readme.io/13cf21f-seq_cstore.png "seq_cstore.png")
  </article>
</details>

> 📘 Note on Alfamart
>
> By accepting Alfamart payment method, your customer can pay in all Alfamart group stores, including Alfamidi and DAN+DAN.

<br />

***

# <b> Sandbox Environment</b>

<br />

The steps given below uses Midtrans *Sandbox* environment to test the integration process. Please make sure that you use the *Server Key* and *Client Key* for the *Sandbox* environment. For more details, refer to [Retrieving API Access Keys](/docs/midtrans-account#retrieving-api-access-keys).

<br />

***

# <b>Steps for Integration</b>

<br />

To integrate with *Over the Counter* payment method, follow the steps given below.

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

### Sample Request and Request Body

The sample CURL request for *Charge API* for *Over the Counter* payment method is shown below. You may  implement according to your backend language. For more details, refer to available [Language Libraries](/docs/midtrans-api-libraries-plugins#language-library).

```curl Alfamart
curl -X POST \
  https://api.sandbox.midtrans.com/v2/charge \
  -H 'Accept: application/json' \
  -H 'Authorization: Basic <YOUR SERVER KEY ENCODED in Base64>' \
  -H 'Content-Type: application/json' \
  -d '{
  "payment_type": "cstore",
  "transaction_details": {
      "order_id": "order-101",
      "gross_amount": 44000
  }
  "cstore" : {
    "store" : "alfamart",
    "message" : "Message ",
    "alfamart_free_text_1": "1st row of receipt,",
    "alfamart_free_text_2": "This is the 2nd row,",
    "alfamart_free_text_3": "3rd row. The end."
  }
}'
```

```curl Indomaret
curl -X POST \
  https://api.sandbox.midtrans.com/v2/charge \
  -H 'Accept: application/json' \
  -H 'Authorization: Basic <YOUR SERVER KEY ENCODED in Base64>' \
  -H 'Content-Type: application/json' \
  -d '{
  "payment_type": "cstore",
  "transaction_details": {
      "order_id": "order-101",
      "gross_amount": 44000
  },
  "cstore" : {
    "store" : "indomaret",
    "message" : "Message to display"
  }
}'
```

<details>
  <summary>POST Body JSON Attribute Description for Alfamart</summary>

  <article>
    | Element                 | Description                                                          | Type        | Required |
    | ----------------------- | -------------------------------------------------------------------- | ----------- | -------- |
    | payment\_type           | Payment type.                                                        | String      | Required |
    | transaction\_details    | The details of the transaction like the order\_id and gross\_amount. | Object      | Required |
    | order\_id               | The order\_id of the transaction.                                    | String      | Required |
    | gross\_amount           | The total amount of transaction.                                     | Long        | Required |
    | cstore                  | Convenience store details.                                           | Object      | Required |
    | store                   | The name of the convenience store.                                   | String (20) | Required |
    | message                 | Label displayed in Alfamart POS.                                     | String (20) | Optional |
    | alfamart\_free\_text\_1 | Customizable first row of text on the printed receipt.               | String (40) | Optional |
    | alfamart\_free\_text\_2 | Customizable second row of text on the printed receipt.              | String (40) | Optional |
    | alfamart\_free\_text\_3 | Customizable third row of text on the printed receipt.               | String (40) | Optional |
  </article>
</details>

<details>
  <summary>POST Body JSON Attribute Description for Indomaret</summary>

  <article>
    | Element              | Description                                                          | Type        | Required |
    | -------------------- | -------------------------------------------------------------------- | ----------- | -------- |
    | payment\_type        | Payment type.                                                        | String      | Required |
    | transaction\_details | The details of the transaction like the order\_id and gross\_amount. | Object      | Required |
    | order\_id            | The order\_id of the transaction.                                    | String      | Required |
    | gross\_amount        | The total amount of transaction.                                     | Long        | Required |
    | cstore               | Convenience store details.                                           | Object      |          |
    | store                | The name of the convenience store.                                   | String (20) | Required |
    | message              | Label to be displayed in Indomaret POS.                              | String (20) | Optional |
  </article>
</details>

<br />

> 📘 Tips
>
> You can [include more information](/docs/coreapi-advanced-features#recommended-parameters) such as `customer_details`, `item_details`, and so on. It is recommended to send more details regarding the transaction, so that these details will be captured on the transaction record. Which can be \[viewed on the Midtrans Dashboard]\(/docs/midtrans-dashboard-usage#transaction.

Learn more on why this API request [should be securely managed from your backend](/docs/payment-security#keep-sensitive-parameters-secured).

<br />

### Sample Response and Response Body

```json Alfamart
{
    "status_code": "201",
    "status_message": "Success, cstore transaction is successful",
    "transaction_id": "d615df87-c96f-4f5c-9d35-2d740d54c1a9",
    "order_id": "order-101o-1578557780",
    "merchant_id": "G812785002",
    "gross_amount": "162500.00",
    "currency": "IDR",
    "payment_type": "cstore",
    "transaction_time": "2020-01-09 15:16:19",
    "transaction_status": "pending",
    "fraud_status": "accept",
    "payment_code": "8127740588870520",
    "store": "alfamart"
}
```

```json Indomaret
{
  "status_code": "201",
  "status_message": "Success, cstore transaction is successful",
  "transaction_id": "9b3951a4-da50-4089-86df-161d3e9251df",
  "order_id": "order-101n-1578557719",
  "gross_amount": "44000.00",
  "currency": "IDR",
  "payment_type": "cstore",
  "transaction_time": "2020-01-09 15:15:19",
  "transaction_status": "pending",
  "merchant_id": "G812785002",
  "payment_code": "578112341234",
  "store": "indomaret"
}
```

<details>
  <summary> Response Body JSON Attribute Description for Alfamart</summary>

  <article>
    | Element             | Description                                                          | Type   | Notes                                                                                          |
    | ------------------- | -------------------------------------------------------------------- | ------ | ---------------------------------------------------------------------------------------------- |
    | status\_code        | This is the status of the API call.                                  | String | For more details, refer to [Error Code and Response Code](/docs/error-code-and-response-code). |
    | status\_message     | A message describing the status of the transaction.                  | String |                                                                                                |
    | transaction\_id     | The *Transaction ID* of the specific transaction.                    | String |                                                                                                |
    | order\_id           | The specific *Order ID*.                                             | String |                                                                                                |
    | gross\_amount       | The total amount of transaction for the specific order.              | String |                                                                                                |
    | currency            | The unit of currency used for the transaction.                       | String |                                                                                                |
    | payment\_type       | The type of payment method used by the customer for the transaction. | String |                                                                                                |
    | transaction\_time   | The date and time at which the transaction occurred.                 | String | It is in the format, *YYYY-MM-DD* *HH:MM:SS.*<br />Time zone: Western Indonesian Time (GMT+7)  |
    | transaction\_status | The status of the transaction.                                       | String | For more details, refer to [Transaction Status](/docs/get-status-api-requests).                |
    | merchant\_id        | Your merchant ID.                                                    | String |                                                                                                |
    | payment\_code       | The code required for making payment at the convenience store.       | String |                                                                                                |
    | store               | The name of the convenience store.                                   | String |                                                                                                |
  </article>
</details>

<details>
  <summary>Response Body JSON Attribute Description for Indomaret</summary>

  <article>
    | Element             | Description                                                          | Type   | Notes                                                                                              |
    | ------------------- | -------------------------------------------------------------------- | ------ | -------------------------------------------------------------------------------------------------- |
    | status\_code        | This is the status of the API call.                                  | String | For more details, refer to [Error Code and Response Code](/docs/error-code-and-response-code).     |
    | status\_message     | A message describing the status of the transaction.                  | String |                                                                                                    |
    | transaction\_id     | The *Transaction ID* of the specific transaction.                    | String |                                                                                                    |
    | order\_id           | The specific *Order ID*.                                             | String |                                                                                                    |
    | gross\_amount       | The total amount of transaction for the specific order.              | String |                                                                                                    |
    | currency            | The unit of currency used for the transaction.                       | String |                                                                                                    |
    | payment\_type       | The type of payment method used by the customer for the transaction. | String |                                                                                                    |
    | transaction\_time   | The date and time at which the transaction occurred.                 | String | It is in the format, *YYYY-MM-DD* *HH:MM:SS.*<br />Time zone: Western Indonesian Time (GMT+7)      |
    | transaction\_status | The status of the transaction.                                       | String | For more details, refer to [Transaction Status](/docs/get-status-api-requests#transaction-status). |
    | merchant\_id        | Your merchant ID.                                                    | String |                                                                                                    |
    | payment\_code       | The code required for making payment at the convenience store.       | String |                                                                                                    |
    | store               | The name of the convenience store.                                   | String |                                                                                                    |
  </article>
</details>

<br />

> 📘 Note
>
> The `payment_code` attribute for the transaction is received.

<br />

### Status Codes and Errors

| Code | Description                            | Notes                                                     |
| ---- | -------------------------------------- | --------------------------------------------------------- |
| 201  | Successful transaction.                | –                                                         |
| 400  | The `transaction_details` are missing. | Make sure the `order_id` and `gross_amount` are included. |
| 413  | There is syntax error.                 | Check the syntax.                                         |
| 500  | Internal system error occurred.        | You can try again later.                                  |

<br />

## 2. Displaying payment code

<br />

To display the payment code on merchant frontend, use `payment_code` retrieved from the previous step.\
The customer can proceed with actual payment, at the nearest convenience store by confirming the payment code and the transaction amount. The payment code is shown on merchant website or apps.

For more details, refer to [Testing Payment on Sandbox](/docs/testing-payment-on-sandbox#convenience-store).

<br />

## 3. Handling transaction notification

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

    ![](https://files.readme.io/33ac75b-core-api-payment-notification-1.png "core-api-payment-notification-1.png")

    <br />

    The *Payment Notification URL* is configured.
  </article>
</details>

<br />

The sample HTTP notification request received at merchant backend for *Over the Counter* payment method is given below.

```json Alfamart
{
  "transaction_time": "2020-01-09 15:16:19",
  "transaction_status": "settlement",
  "transaction_id": "d615df87-c96f-4f5c-9d35-2d740d54c1a9",
  "store": "alfamart",
  "status_message": "midtrans payment notification",
  "status_code": "200",
  "signature_key": "6fd1210e6e4b60d7cc3862780016b110f9d3d56e291172c69b0bbfd60d380be22ad02f1d48bbabeeb81a882d7abbd3d6fa94207bede9132adc1a773489dfd0c8",
  "settlement_time": "2020-01-09 15:20:09",
  "payment_type": "cstore",
  "payment_code": "8127740588870520",
  "order_id": "order-101o-1578557780",
  "merchant_id": "G812785002",
  "gross_amount": "162500.00",
  "fraud_status": "accept",
  "currency": "IDR"
}
```

```json Indomaret
{
  "transaction_time": "2020-01-09 15:15:19",
  "transaction_status":
"settlement",
  "transaction_id": "9b3951a4-da50-4089-86df-161d3e9251df",
  "store": "indomaret",
  "status_message": "midtrans payment notification",
  "status_code": "200",
  "signature_key": "82faa4ab71128bf8f1b13359003688409e5656bae0bb2f39669f7685e3af26ce6f676384fd05cca9349d155ab8b08789761cb399e5530bf2e68d414b8856be0d",
  "settlement_time": "2020-01-09 15:22:07",
  "payment_type": "cstore",
  "payment_code": "578112341234",
  "order_id": "order-101n-1578557719",
  "merchant_id": "G812785002",
  "gross_amount": "44000.00",
  "currency": "IDR",
  "approval_code": "45682001084123432248"
}
```

See also :  [HTTP(S) Notification/Webhooks](/docs/https-notification-webhooks)

<br />

***

# <b> Switching to Production Environment</b>

<br />

Follow the steps given below to switch to Midtrans *Production* environment and to accept real payments from real customers.

1. Change API domain URL from `api.sandbox.midtrans.com` to `api.midtrans.com`.
2. Use *Client Key* and *Server Key* for *Production* environment. For more details, refer to [Retrieving API Access Keys](/docs/midtrans-account#retrieving-api-access-keys).

<br />