# Integration: Card Payment

*Card* payment is one of the payment methods offered by Midtrans. Using this payment method, customers can make payments using a credit card or any online-transaction-capable debit card within Visa, MasterCard, JCB, or Amex network. Midtrans sends real-time notification when the customer completes the payment.

> 📘
>
> Please create a Midtrans [Merchant Administrative Portal (MAP) account](/docs/midtrans-account) before proceeding to the integration process.

<br />

<details>
  <summary><b>Sequence Diagram</b></summary>

  <article>
    The end-to-end payment process for Card Transaction (3DS) is illustrated in the sequence diagram given below.

    ![](https://files.readme.io/0a7047f-core_api-sequence_3ds.png "core_api-sequence_3ds.png")
  </article>
</details>

<br />

***

# <b>Steps for Integration</b>

<br />

<br />

## 1. Getting the Card Token

<br />

<br />

### Including Midtrans JS Library

Midtrans JS library can be included on your implementation of payment page's HTML, by adding the following script tag.

```html
<script id="midtrans-script" type="text/javascript"
src="https://api.midtrans.com/v2/assets/js/midtrans-new-3ds.min.js"
data-environment="sandbox"
data-client-key="<INSERT YOUR CLIENT KEY HERE>"></script>
```

Enter the values of attributes as given below.

| Attribute          | Value                     | Note                                                                                                        |
| ------------------ | ------------------------- | ----------------------------------------------------------------------------------------------------------- |
| `data-environment` | `sandbox` or `production` | Enter the values depending on the environment.                                                              |
| `data-client-key`  | Client key                | For more details, refer to [Retrieving API Access Keys](/docs/midtrans-account#retrieving-api-access-keys). |

For more details about the API, refer to [Get Token](/reference/get-token).

Note: If you are using frontend framework such as ReactJS and struggling to include the script tag, please [refer to this recommendation](/docs/technical-faq#my-developer-uses-react-js-frontend-framework-and-is-unable-to-use-midtransminjssnapjs-what-should-i-do).

<br />

### Get Card Token JS Implementation

Midtrans uses `MidtransNew3ds.getCardToken` function to retrieve card `token_id`. Implement the following JavaScript on your payment page's HTML.

```javascript
// card data from customer input, for example
var cardData = {
  "card_number": 4811111111111114,
  "card_exp_month": 02,
  "card_exp_year": 2025,
  "card_cvv": 123,
  "bank_one_time_token": "12345678"
};

// callback functions
var options = {
  onSuccess: function(response){
    // Success to get card token_id, implement as you wish here
    console.log('Success to get card token_id, response:', response);
    var token_id = response.token_id;

    console.log('This is the card token_id:', token_id);
    // Implement sending the token_id to backend to proceed to next step
  },
  onFailure: function(response){
    // Fail to get card token_id, implement as you wish here
    console.log('Fail to get card token_id, response:', response);

    // you may want to implement displaying failure message to customer.
    // Also record the error message to your log, so you can review
    // what causing failure on this transaction.
  }
};

// trigger `getCardToken` function
MidtransNew3ds.getCardToken(cardData, options);
```

<br />

Use the following credentials to test the *Card* payment method.

| Name                | Value                                                 |
| ------------------- | ----------------------------------------------------- |
| Card Number         | `4811 1111 1111 1114`                                 |
| CVV                 | `123`                                                 |
| Exp Month           | Any month in MM format. For example, `02`.            |
| Exp Year            | Any future year, in YYYY format. For example, `2025`. |
| OTP/3DS             | `112233`                                              |
| Bank One Time Token | `12345678`                                            |

For more details, refer to [Testing Payments on Sandbox](/docs/testing-payment-on-sandbox).

<br />

### Get Card Token Response

The `token_id` retrieved from `response` object inside `onSuccess` callback function, is used as one of JSON parameters for Charge API Request.

`token_id` is then passed from frontend to backend. It can be done using AJAX via JavaScript, HTML POST or any other implementation of your choice.

> 📘 Note
>
> The `token_id` is valid for one transaction only. The process of getting `token_id` is repeated for every transaction, to ensure secure transmission of card data. To save card token, you may use [One-click](/reference/card-feature-one-click) / [Two-clicks](/reference/card-feature-two-clicks) feature.

> Sample Response

```json Success Response
{
  "status_code": "200",
  "status_message": "Credit card token is created as Token ID.",
  "token_id": "48111111-1114-77328ff4-eba6-4201-b31a-1070d8f19ae9",
  "hash": "48111111-1114-xxxx"
}
```

```json Failure Response
{
  "status_code": "400",
  "status_message": "One or more parameters in the payload is invalid.",
  "validation_messages": [
    "This card is not supported for online transactions. Please contact your bank",
    "card_number does not match with Luhn algorithm"
  ],
  "id": "02197189-7cab-4006-8379-51edcd0a253b"
}
```

<br />

## 2. Sending Transaction Data to Charge API

<br />

The *Charge API* request is sent from the merchant backend, with the `transaction_details` and the `token_id`.\ <br />\
The table given below describes some required components.

| Element        | Description                                                                                                                                           | Type     |
| -------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------- | -------- |
| Server Key     | The unique ID retrieved from *Dashboard*. For more details, refer to [Retrieving API Access Keys](/docs/midtrans-account#retrieving-api-access-keys). | String   |
| order\_id      | The order\_id of the transaction.                                                                                                                     | String   |
| gross\_amount  | The total amount of transaction.                                                                                                                      | Long Int |
| token\_id      | The token\_id retrieved from [Getting the Card Token](/docs/coreapi-card-payment-integration#1-getting-the-card-token).                               | String   |
| authentication | Flag to enable the 3D secure authentication.                                                                                                          | Boolean  |

> 📘 Note
>
> For better security and fraud prevention, set `authentication` to `true`. Set the `authentication` to `false` only after confirming with Midtrans and the acquiring bank.

<br />

### Sample Request

The sample requests for *Charge API* for *Card* payment method are shown below. You may implement according to your backend language. For more details, refer to available [Language Libraries](/docs/midtrans-api-libraries-plugins).\ <br />

### Endpoints

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

```curl
curl -X POST \
  https://api.sandbox.midtrans.com/v2/charge \
  -H 'Accept: application/json'\
  -H 'Authorization: Basic <YOUR SERVER KEY ENCODED in Base64>' \
  -H 'Content-Type: application/json' \
  -d '{
  	"payment_type": "credit_card",
  	"transaction_details": {
    	"order_id": "order102",
    	"gross_amount": 789000
  	},
  	"credit_card": {
    	"token_id": "<token_id from Get Card Token Step>",
    	"authentication": true,
  	}
    "customer_details": {
        "first_name": "budi",
        "last_name": "pratama",
        "email": "budi.pra@example.com",
        "phone": "08111222333"
    }
}'
```

```php
/*Install Midtrans PHP Library (https://github.com/Midtrans/midtrans-php)
composer require midtrans/midtrans-php
                              
Alternatively, if you are not using **Composer**, you can download midtrans-php library (https://github.com/Midtrans/midtrans-php/archive/master.zip), and then require the file manually.                                                                                
require_once dirname(__FILE__) . '/pathofproject/Midtrans.php'; */

//SAMPLE REQUEST START HERE

// Set your Merchant Server Key
\Midtrans\Config::$serverKey = 'YOUR_SERVER_KEY';

$params = array(
    'transaction_details' => array(
        'order_id' => rand(),
        'gross_amount' => 10000,
    ),
	'payment_type' => 'credit_card',
    'credit_card'  => array(
        'token_id'      => $_POST['token_id'],
        'authentication'=> true,
    ),
    'customer_details' => array(
        'first_name' => 'budi',
        'last_name' => 'pratama',
        'email' => 'budi.pra@example.com',
        'phone' => '08111222333',
    ),
);

$response = \Midtrans\CoreApi::charge($params);
```

```javascript
/*Install midtrans-client (https://github.com/Midtrans/midtrans-nodejs-client) NPM package.
npm install --save midtrans-client*/

//SAMPLE REQUEST START HERE


const midtransClient = require('midtrans-client');
// Create Core API instance
let core = new midtransClient.CoreApi({
        isProduction : false,
        serverKey : 'YOUR_SERVER_KEY',
        clientKey : 'YOUR_CLIENT_KEY'
    });

let parameter = {
    "payment_type": "credit_card",
    "transaction_details": {
        "gross_amount": 12145,
        "order_id": "test-transaction-54321",
    },
    "credit_card":{
        "token_id": 'CREDIT_CARD_TOKEN', // change with your card token
        "authentication": true
    }
};

// charge transaction
core.charge(parameter)
    .then((chargeResponse)=>{
        console.log('chargeResponse:');
        console.log(chargeResponse);
    });
```

```java
/*Install midtrans-java (https://github.com/Midtrans/midtrans-java) library.
If you are using Maven as the build automation tool for your project, please add the following dependency to your project's build definition (pom.xml).
<dependencies>
    <dependency>
      <groupId>com.midtrans</groupId>
      <artifactId>java-library</artifactId>
      <version>3.0.0</version>
    </dependency>
</dependencies>
  
If you are using Gradle as the build tool for your project, please add the following dependency to your project's build definition (build.gradle).

dependencies {
	implementation 'com.midtrans:java-library:3.0.0'
}

You can also check the functional test (https://github.com/Midtrans/midtrans-java/blob/master/library/src/test/java/com/midtrans/java/CoreApiTest.java) for more examples.
*/

//SAMPLE REQUEST START HERE
import com.midtrans.Midtrans;
import com.midtrans.httpclient.SnapApi;
import com.midtrans.httpclient.error.MidtransError;

import java.util.HashMap;
import java.util.Map;
import java.util.UUID;
import org.json.JSONObject;


public class MidtransExample {

    public static void main(String[] args) throws MidtransError {
      // Set serverKey to Midtrans global config
      Midtrans.serverKey = "YOUR_SERVER_KEY";

      // Set value to true if you want Production Environment (accept real transaction).
      Midtrans.isProduction = false;

		// Create Function JSON Raw Object
		public Map<String, Object> requestBody() {
		    UUID idRand = UUID.randomUUID();
		    Map<String, Object> params = new HashMap<>();

		    Map<String, String> transactionDetails = new HashMap<>();
		    transactionDetails.put("order_id", idRand);
		    transactionDetails.put("gross_amount", "265000");

		    Map<String, String> creditCard = new HashMap<>();
		    creditCard.put("token_id", YOUR_CARD_TOKEN_ID);
		    creditCard.put("authentication", "true");

		    params.put("transaction_details", transactionDetails);
		    params.put("credit_card", creditCard);

		    return params;
		}

		// charge transaction
		JSONObject result = CoreApi.chargeTransaction(requestBody());
		System.out.println(result);
    }
}
```

```python
#Install midtransclient(https://github.com/Midtrans/midtrans-python-client) PIP package
#pip install midtransclient

#SAMPLE REQUEST START HERE

import midtransclient
# Create Core API instance
core_api = midtransclient.CoreApi(
    is_production=False,
    server_key='YOUR_SERVER_KEY',
    client_key='YOUR_CLIENT_KEY'
)
# Build API parameter
param = {
    "payment_type": "credit_card",
    "transaction_details": {
        "gross_amount": 12145,
        "order_id": "test-transaction-54321",
    },
    "credit_card":{
        "token_id": 'CREDIT_CARD_TOKEN', # change with your card token
        "authentication": True
    }
}

# charge transaction
charge_response = core_api.charge(param)
```

Please also check Midtrans [available language libraries](/docs/midtrans-api-libraries-plugins).

> 📘 Tips
>
> You can [include more information](/docs/coreapi-advanced-features#recommended-parameters) such as `customer_details`, `item_details`, and so on. It is recommended to send more details regarding the transaction, so that these details will be captured on the transaction record. Which can be [viewed on the Midtrans Dashboard](/docs/midtrans-dashboard-usage#transaction).

Learn more on why this API request [should be securely managed from your backend](/docs/payment-security#keep-your-server-key-secured).

<br />

### Sample Response

```json
{
  "status_code": "201",
  "status_message": "Success, Credit Card transaction is successful",
  "transaction_id": "0bb563a9-ebea-41f7-ae9f-d99ec5f9700a",
  "order_id": "order102",
  "redirect_url": "https://api.sandbox.veritrans.co.id/v2/token/rba/redirect/48111111-1114-0bb563a9-ebea-41f7-ae9f-d99ec5f9700a",
  "gross_amount": "789000.00",
  "currency": "IDR",
  "payment_type": "credit_card",
  "transaction_time": "2019-08-27 15:50:54",
  "transaction_status": "pending",
  "fraud_status": "accept",
  "masked_card": "48111111-1114",
  "bank": "bni",
  "card_type": "credit",
  "three_ds_version": "2",
  "challenge_completion": true
}
```

<article>
  <details>
    <summary>Response Body JSON Attribute Description</summary>

    | Element             | Description                                                          | Type   | Notes                                                                                          |
    | ------------------- | -------------------------------------------------------------------- | ------ | ---------------------------------------------------------------------------------------------- |
    | status\_code        | The status of the API call.                                          | String | For more details, refer to [Error Code and Response Code](/docs/error-code-and-response-code). |
    | status\_message     | A message describing the status of the transaction.                  | String | --                                                                                             |
    | transaction\_id     | The *Transaction ID* of the specific transaction.                    | String | --                                                                                             |
    | order\_id           | The specific *Order ID*.                                             | String | --                                                                                             |
    | redirect\_url       | The redirect URL.                                                    | String | --                                                                                             |
    | gross\_amount       | The total transaction amount for the specific order.                 | String | --                                                                                             |
    | currency            | The unit of currency used for the transaction.                       | String | --                                                                                             |
    | payment\_type       | The type of payment method used by the customer for the transaction. | String | --                                                                                             |
    | transaction\_time   | The date and time at which the transaction occurred.                 | String | It is in the format, *YYYY-MM-DD* *HH:MM:SS.*<br />Time zone: Western Indonesian Time (GMT+7). |
    | transaction\_status | The status of the transaction.                                       | String | For more details, refer to [Transaction Status](/docs/get-status-api-requests).                |
    | fraud\_status       | The fraud\_status of the transaction.                                | String | --                                                                                             |
    | masked\_card        | The partial card number of the custo.                                | String | --                                                                                             |
    | bank                | The name of the acquiring bank which process the transaction.        | String | --                                                                                             |
    | card\_type          | The type of the card.                                                | String | --                                                                                             |

    > 📘
    >
    > The `redirect_url` attribute for the transaction is received.
  </details>
</article>

> 📘 Notes
>
> If the `transaction_status` is `capture` and `fraud_status` is `accept`, it means that the transaction does not requires 3DS. The transaction is successfully completed.<br />If the `transaction_status` is `pending` and `redirect_url` exists, it means the transaction requires 3DS. Open 3DS authentication page.

<br />

<details>
  <summary><b>Status Codes and Errors</b></summary>

  <article>
    | Status Code | Description                                                                                    | Sample Response Message                                                                                                                                                                                                         |
    | ----------- | ---------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
    | 200         | Successful transaction (non 3DS transaction).                                                  | "transaction\_status": "capture"                                                                                                                                                                                                |
    | 201         | Need to open the redirect\_url (3DS transaction).                                              | "[https://api.sandbox.veritrans.co.id/v2/token/rba/redirect/48111111-1114-f424a955-ed0f-4a64-88ea-60cdc9655984](https://api.sandbox.veritrans.co.id/v2/token/rba/redirect/48111111-1114-f424a955-ed0f-4a64-88ea-60cdc9655984) " |
    | 401         | Failed transaction. Wrong authorization details sent.                                          | "Access denied, please check client or server key"                                                                                                                                                                              |
    | 4xx         | Failed transaction. Wrong parameter sent. Follow the `error_message` and check your parameter. | "transaction\_details.gross\_amount is not equal to the sum of item\_details"                                                                                                                                                   |
    | 5xx         | Failed transaction. Midtrans internal error. This is temporary. Retry again later.             | "Sorry, we encountered internal server error. We will fix this soon."                                                                                                                                                           |
  </article>
</details>

<br />

## 3. Opening 3DS Authentication Page

<br />

To open 3DS authentication page on merchant frontend, display the `redirect_url` retrieved from previous step. The redirect URL is displayed using `MidtransNew3ds.authenticate` or `MidtransNew3ds.redirect` function in [Midtrans New 3DS JS library](https://api.midtrans.com/v2/assets/js/midtrans-new-3ds.min.js).

<br />

### Open 3DS Authentication Page JS Implementation

```html HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sample</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/picomodal/3.0.0/picoModal.min.js"></script>
    <script id="midtrans-script" type="text/javascript" src="https://api.midtrans.com/v2/assets/js/midtrans-new-3ds.min.js" data-environment="sandbox" data-client-key="<INSERT YOUR CLIENT KEY HERE>"></script>
</head>
<body>
    <script>
        /**
         * Example helper functions to open Iframe popup, you may replace this with your own 
         * method of open iframe. In this example, PicoModal library is used (first include/import this: https://cdnjs.cloudflare.com/ajax/libs/picomodal/3.0.0/picoModal.min.js)
         */
        var popupModal = (function(){
            var modal = null;
            return {
                openPopup(url){
                  modal = picoModal({
                      content:'<iframe frameborder="0" style="height:90vh; width:100%;" src="'+url+'"></iframe>',
                      width: "75%",
                      closeButton: false,
                      overlayClose: false,
                      escCloses: false
                  }).show();
                },
                closePopup(){
                  try{
                      modal.close();
                  } catch(e) {}
                }
            }
        }());
    </script>

    <script>
        // pass the value of redirect_url from Charge API Response from your backend on previous step.
        var redirect_url = '<redirect_url Retrieved from Charge API Response>'; 

        // callback functions
        var options = {
            performAuthentication: function(redirect_url){
                // Implement how you will open iframe to display 3ds authentication redirect_url to customer
                popupModal.openPopup(redirect_url);
            },
            onSuccess: function(response){
                // 3ds authentication success, implement payment success scenario
                console.log('response:',response);
                popupModal.closePopup();
                // // Simulate an HTTP redirect:
                window.location.replace("http://midtrans.com?status=success");
            },
            onFailure: function(response){
                // 3ds authentication failure, implement payment failure scenario
                console.log('response:',response);
                popupModal.closePopup();
            },
            onPending: function(response){
                // transaction is pending, transaction result will be notified later via 
                // HTTP POST notification, implement as you wish here
                console.log('response:',response);
                popupModal.closePopup();
            }
        };

        // trigger `authenticate` function
        MidtransNew3ds.authenticate(redirect_url, options);
    </script>   
</body>
</html>
```

```javascript
var redirect_url = '<redirect_url Retrieved from Charge Response>';

// callback functions
var options = {
  performAuthentication: function(redirect_url){
    // Implement how you will open iframe to display 3ds authentication redirect_url to customer
    popupModal.openPopup(redirect_url);
  },
  onSuccess: function(response){
    // 3ds authentication success, implement payment success scenario
    console.log('response:',response);
    popupModal.closePopup();
  },
  onFailure: function(response){
    // 3ds authentication failure, implement payment failure scenario
    console.log('response:',response);
    popupModal.closePopup();
  },
  onPending: function(response){
    // transaction is pending, transaction result will be notified later via 
    // HTTP POST notification, implement as you wish here
    console.log('response:',response);
    popupModal.closePopup();
  }
};

// trigger `authenticate` function
MidtransNew3ds.authenticate(redirect_url, options);

/**
 * Example helper functions to open Iframe popup, you may replace this with your own 
 * method of open iframe. In this example, PicoModal library is used by including 
 * this script tag on the HTML:
 * <script src="https://cdnjs.cloudflare.com/ajax/libs/picomodal/3.0.0/picoModal.js"></script>
 */
var popupModal = (function(){
  var modal = null;
  return {
    openPopup(url){
      modal = picoModal({
        content:'<iframe frameborder="0" style="height:90vh; width:100%;" src="'+url+'"></iframe>',
        width: "75%",
        closeButton: false,
        overlayClose: false,
        escCloses: false
      }).show();
    },
    closePopup(){
      try{
        modal.close();
      } catch(e) {}
    }
  }
}());

/**
 * Alternatively, instead of opening 3ds authentication redirect_url using iframe,
 * you can also redirect customer using:
 * MidtransNew3ds.redirect( redirect_url, { callbackUrl : 'https://mywebsite.com/finish_3ds' });
 **/
```

<br />

### 3DS Authentication Page JSON Response

On the JS callback function, we will get the transaction details as JSON response as given below.

```json Success Response
{
  "status_code": "200",
  "status_message": "Success, Credit Card transaction is successful",
  "channel_response_code": "00",
  "channel_response_message": "Approved",
  "bank": "bni",
  "eci": "05",
  "transaction_id": "405d27d5-5ad9-43ac-bdd6-0ccbde7d7dda",
  "order_id": "test-transaction-54321",
  "merchant_id": "G490526303",
  "gross_amount": "100000.00",
  "currency": "IDR",
  "payment_type": "credit_card",
  "transaction_time": "2020-08-12 16:04:23",
  "transaction_status": "capture",
  "fraud_status": "accept",
  "approval_code": "1597223068747",
  "masked_card": "48111111-1114",
  "card_type": "credit",
  "three_ds_version": "2",
  "challenge_completion": true
}
```

```json Failure Response
{
  "status_code": "202",
  "status_message": "Card is not authenticated.",
  "bank": "bni",
  "eci": "07",
  "transaction_id": "1063cc1f-f07e-4755-ab85-19a4592de097",
  "order_id": "test-transaction-54321",
  "merchant_id": "G490526303",
  "gross_amount": "100000.00",
  "currency": "IDR",
  "payment_type": "credit_card",
  "transaction_time": "2020-08-12 16:03:49",
  "transaction_status": "deny",
  "fraud_status": "accept",
  "masked_card": "48111111-1114",
}
```

If the `transaction_status` is `capture` and `fraud_status` is `accept`, it means the transaction is successfully completed.

If the `transaction_status` is `pending`, it means the transaction is waiting for further external action (by customer to complete 3DS process, and/or by 3DS provider to verify it).

> 📘 Note
>
> To update the **Transaction Status** on merchant backend/database, **do not** solely rely on frontend callbacks. For security reasons, to make sure that the **Transaction Status** is authentically coming from Midtrans, you can update **Transaction Status** by waiting for [HTTP Notification](/docs/https-notification-webhooks) or timely call [API Get Status](/docs/get-status-api-requests) on your backend.

Specific if the transaction is processed via [3DS 2](/reference/card-feature-3d-secure-20-emv-3ds) (when the acquiring bank and the MID support), there's small possibility of the transaction is still waiting for the card's 3DS provider to process/verify it, which it will result in transaction*status`pending` within the frontend callback. To get confirmation of payment success, you can follow \_Note* above.

<br />

## 4. Handling After Payment

<br />

<br />

<details>
  <summary><b>Configuring Payment Notification URL</b></summary>

  <article>
    To configure the Payment Notification URL, follow the steps given below.

    1. Login to your MAP account.
    2. On the Home page, go to **SETTINGS > CONFIGURATION**.\
       *Configuration* page is displayed.
    3. Enter **Payment Notification URL**.
    4. Click **Update**. A confirmation message is displayed.

    ![](https://files.readme.io/f405341-core-api-payment-notification-1.png "core-api-payment-notification-1.png")

    The *Payment Notification URL* is configured.
  </article>
</details>

Also read about [HTTP(S) Notification/Webhooks](/docs/https-notification-webhooks).

<br />

***

## <b>Description of Transaction Status</b>

<br />

| Transaction Status | Description                                                                                                                                         |
| ------------------ | --------------------------------------------------------------------------------------------------------------------------------------------------- |
| `capture`          | The transaction is successful. Funds have been deducted from the customers' account.                                                                |
| `pending`          | The transaction is initiated and is waiting for further external action (by customer to complete 3DS process, and/or by 3DS provider to verify it). |
| `deny`             | The transaction is denied. <br />Check `channel_response_message` or `fraud_status` for details.                                                    |
| `expire`           | The transaction failed, because customer did not complete 3DS within the expiry time.                                                               |

For more details, refer to [Midtrans Transaction Status Cycle Description](/docs/transaction-status-cycle).