# Transaction Status Cycle

Midtrans Transaction Status Cycle Description

The table given below describes the life cycle of *Transaction Status* and its possible changes.

***

<br />

## Transaction Status

<br />

<Table>
  <thead>
    <tr>
      <th>
        Transaction Status
      </th>

      <th>
        Description
      </th>

      <th>
        Possible changes(s)
      </th>
    </tr>
  </thead>

  <tbody>
    <tr>
      <td>
        `pending`
      </td>

      <td>
        Transaction is created and available/waiting to be paid by customer at the payment provider (ATM/Internet banking/E-Wallet app/store). For card payment method: waiting for customer to complete (and card issuer to validate) 3DS/OTP process.
      </td>

      <td>
        settlement, <br />expire,<br />cancel, <br />deny
      </td>
    </tr>

    <tr>
      <td>
        `capture`
      </td>

      <td>
        Transaction is successful and credit card balance is captured successfully. <br />If no action is taken by you, the transaction will be successfully settled on the next day and transaction status will change to *settlement*.<br />It is safe to assume a successful payment.
      </td>

      <td>
        settlement, <br />cancel
      </td>
    </tr>

    <tr>
      <td>
        `settlement`
      </td>

      <td>
        Transaction is successfully settled. Funds have been received.
      </td>

      <td>
        refund, chargeback, partial\_refund, partial\_chargeback, deny\*
      </td>
    </tr>

    <tr>
      <td>
        `deny`
      </td>

      <td>
        The credentials used for payment are rejected by the payment provider or Midtrans Fraud Detection System (FDS). <br />To know the reason and details for denied transaction, see the `status_message` field in the response.
      </td>

      <td />
    </tr>

    <tr>
      <td>
        `cancel`
      </td>

      <td>
        Transaction is cancelled. Can be triggered by Midtrans or merchant themselves.<br />Cancelled transaction can be caused by various reasons:<br /> 1. `Capture` transaction is cancelled before Settlement.
      </td>

      <td />
    </tr>

    <tr>
      <td>
        `expire`
      </td>

      <td>
        Transaction no longer available to be paid or processed, because the payment is not completed within the expiry time period.
      </td>

      <td />
    </tr>

    <tr>
      <td>
        `failure`
      </td>

      <td>
        The transaction status is caused by unexpected error during transaction processing. <br />Failure transaction can be caused by various reasons, but mostly it is caused when bank fails to respond.<br />This occurs rarely.
      </td>

      <td />
    </tr>

    <tr>
      <td>
        `refund`
      </td>

      <td>
        Transaction is marked to be refunded. Refund can be requested by Merchant.
      </td>

      <td />
    </tr>

    <tr>
      <td>
        `chargeback`
      </td>

      <td>
        Transaction is marked to be charged back.
      </td>

      <td />
    </tr>

    <tr>
      <td>
        `partial_refund`
      </td>

      <td>
        Transaction is marked to be partially refunded.
      </td>

      <td />
    </tr>

    <tr>
      <td>
        `partial_chargeback`
      </td>

      <td>
        Transaction is marked to be partially charged back.
      </td>

      <td>
        chargeback
      </td>
    </tr>

    <tr>
      <td>
        `authorize`
      </td>

      <td>
        Only available specifically only if you are using pre-authorize feature for card transactions (an advanced feature that you will not have by default, so in most cases are safe to ignore). Transaction is successful and card balance is reserved (authorized) successfully. You can later perform API “capture” to change it into `capture`, or if no action is taken will be auto released. Depending on your business use case, you may assume `authorize` status as a successful transaction.
      </td>

      <td>
        capture, <br />deny, <br />cancel, <br />expire
      </td>
    </tr>
  </tbody>
</Table>

<br />

### Reversal Case

<br />

Known specific to payment methods of *Permata Bank Transfer, Mandiri Bill Payment,* and *Indomaret*; In some **very rare cases**, there is a possibility where `settlement` can later change into `deny`, also known as *Reversal*. It may happen usually within the span of 1-5 minutes. It is caused by reversal triggered by the corresponding payment provider (issuing/acquiring bank), due to the nature of their payment networks. This status change will trigger [HTTP(s) notification/webhook](https://docs.midtrans.com/docs/https-notification-webhooks) from Midtrans to your server.

The change of status to `deny` means the transaction fund is reversed back to the customer, no fund is received on the merchant side. So merchants should treat the transaction as "not paid" (or rejected) and should not proceed the customer's order (do not deliver the goods/service to the customer). Therefore, please ensure that your notification handling logic can cover this case.

The fund will bounce-back to the customer's account automatically, but it may take time depending on the process/policy of the payment provider and their chain of networks. Customers can be advised to contact the payment provider for the status.

<br />

### Status When Using Snap API

<br />

When a transaction is created on Snap API, it does not immediately assign any payment status on *Core API GET Status* response. Even if the payment page is activated on Snap API, you might encounter `404` or *Payment not found* response while calling *Core API GET Status*.

This is because the customer did not yet choose any payment method within the Snap payment page (idling or abandoning the Snap payment page). Once the customer proceeds with a payment method, then the transaction status is assigned and is available on *Core API GET Status*.

<br />

***

<br />

## Fraud Status

<br />

The table given below describes the Fraud status.

<br />

| Fraud Status | Fund Received | Description                                                                                                                                                                |
| ------------ | ------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `accept`     | ✅             | Transaction is safe to proceed. It is not considered as a fraud.                                                                                                           |
| `deny`       | ❌             | Transaction is considered as fraud. It is rejected by Midtrans FDS. FDS rejected transaction usually will not result in transaction\_status: pending, capture, settlement. |

<br />

***

<br />

## API Action / Method

<br />

You can perform a number of actions on *Transaction Status* through API calls.

The header for all backend requests is described below.

<br />

| Header Name   | Description                                           | Required | Values             |
| ------------- | ----------------------------------------------------- | -------- | ------------------ |
| Accept        | The format of the data to be returned.                | Required | application/json   |
| Content-Type  | The format of the data to be posted                   | Required | application/json   |
| Authorization | The authentication method used to access the resource | Required | Basic AUTH\_STRING |

<br />

**AUTH\_STRING**: For more information, refer to [API Authorization and Header](https://docs.midtrans.com/en/technical-reference/api-header).

<br />

**Example**:

A sample Curl code given for an API request to cancel a pending transaction is given below.

<br />

```bash
curl -X POST \
  https://api.sandbox.midtrans.com/v2/myCustOrder123/cancel \
  -H 'Accept: application/json'\
  -H 'Authorization: Basic U0ItTWlkLXNlcnZlci1UT3ExYTJBVnVpeWhoT2p2ZnMzVV7LZU87' \
  -H 'Content-Type: application/json'
```

<br />

**API Base URL:**

* Sandbox Environment : `https://api.sandbox.midtrans.com`
* Production Environment : `https://api.midtrans.com`

<br />

<Table>
  <thead>
    <tr>
      <th>
        Endpoint URL
      </th>

      <th>
        HTTP Method
      </th>

      <th>
        Description
      </th>
    </tr>
  </thead>

  <tbody>
    <tr>
      <td>
        /v2/\[ORDER\_ID]/status
      </td>

      <td>
        GET
      </td>

      <td>
        Get information status of a transaction with a `order_id`. <br />For more information, refer to [GET Status](https://docs.midtrans.com/reference/get-transaction-status#get-status-transaction-response).
      </td>
    </tr>

    <tr>
      <td>
        /v2/\[ORDER\_ID]/cancel
      </td>

      <td>
        POST
      </td>

      <td>
        Cancel a transaction with `order_id`. <br />Cancelation can only be done before `settlement`. <br />For more information, refer to [Cancel Transaction](https://api-docs.midtrans.com/#cancel-transaction).
      </td>
    </tr>

    <tr>
      <td>
        /v2/\[ORDER\_ID]/refund
      </td>

      <td>
        POST
      </td>

      <td>
        Mark `order_id` with `settlement` status to be marked as `refund`. <br />For more information, refer to [Refund Transaction](https://api-docs.midtrans.com/#refund-transaction).
      </td>
    </tr>

    <tr>
      <td>
        /v2/\[ORDER\_ID]/refund/online/direct
      </td>

      <td>
        POST
      </td>

      <td>
        Attempt to send refund to bank or payment provider directly and update the transaction status to `refund` if it succeeded. <br />For more information, refer to [Direct Refund Transaction](https://api-docs.midtrans.com/#direct-refund-transaction) for more information.
      </td>
    </tr>

    <tr>
      <td>
        /v2/\[ORDER\_ID]/expire
      </td>

      <td>
        POST
      </td>

      <td>
        Update `order_id` with pending status to be marked as `expired`. <br />For more information, refer to [Expire Transactions](https://api-docs.midtrans.com/#expire-transaction).
      </td>
    </tr>

    <tr>
      <td>
        /v2/\[ORDER\_ID]/status/b2b
      </td>

      <td>
        GET
      </td>

      <td>
        Get information status of multiple B2B transactions related to `order_id`. <br />For more information, refer to [GET transaction status for B2B](https://api-docs.midtrans.com/#get-transaction-status-b2b).
      </td>
    </tr>

    <tr>
      <td>
        /v2/capture
      </td>

      <td>
        POST
      </td>

      <td>
        Capture a pre-authorized transaction for card payment. <br />For more information, refer to [Capture Transaction](https://api-docs.midtrans.com/#capture-transaction).
      </td>
    </tr>
  </tbody>
</Table>

<br />

For a full list and details of APIs, refer to [API docs](https://api-docs.midtrans.com/#payment-api).

<br />

> 📘 Note
>
> You can also replace `order_id` used in API urls with `transaction_id`, which uniquely generated from Midtrans side and you received as response when creating transaction, and from HTTP Notification. This is useful if your `order_id` contains unusual character (such as `#`) that may result in an invalid URL pattern.

<br />

> 📘 Tips
>
> Each of the official [Midtrans Language Libraries](https://docs.midtrans.com/docs/midtrans-api-libraries-plugins) has easy-to-use functions implementing most of the endpoints above. Please refer to the library's GitHub Repository for usage example.