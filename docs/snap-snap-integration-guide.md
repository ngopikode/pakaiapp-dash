# Integration Guide

The steps for technical integration of Snap are explained below.

# Preparation

<br />

> 📘 Note
>
> In this section, Midtrans *Sandbox* environment is used to test the integration process.

<br />

#### [Sign Up for Midtrans Account](/docs/midtrans-account)

Sign up for a Midtrans Merchant Administration Portal (MAP) account, to get your API Keys for *Sandbox* environment and to test integration.

#### [Retrieving API Keys](/docs/midtrans-account#retrieving-api-access-keys)

Retrieve Sandbox mode API keys that will be used for this guide.

<br />

***

<br />

# Integration Steps Overview

<br />

1. Acquire Snap transaction token on your backend
2. Display Snap payment page on frontend
3. Customer perform payment on payment page
4. Handling payment status update on your backend

<br />

<details>
  <summary><b>Sequence Diagram</b></summary>

  <article>
    <br />

    The overall Snap end-to-end payment process is illustrated in following sequence diagram:

    <br />

    #### **Snap Popup Mode**

    <Image title="snap_sequence_regular.png" alt={671} align="center" src="https://files.readme.io/99dd584-snap_sequence_regular.png">
      Snap Redirect sequence diagram
    </Image>

    <br />

    #### **Snap Embedded Mode**

    <Image alt="Snap Redirect sequence diagram" align="center" src="https://files.readme.io/1d1326d-Snap_Embedded_Mode.png">
      Snap Redirect sequence diagram
    </Image>

    <br />

    #### **Snap Redirect Mode**

    <Image title="snap_sequence_redirect.png" alt={764} align="center" src="https://files.readme.io/dc06538-snap_sequence_redirect.png">
      Snap Redirect sequence diagram
    </Image>
  </article>
</details>

<br />

You can also refer to various step by step implementation walkthrough [here](https://docs.midtrans.com/recipes).

<br />

***

<br />

# 1. Acquiring Transaction Token on Backend

<br />

API request should be done from merchant backend to acquire Snap transaction `token` by providing payment information and *Server Key*. There are at least three components that are required to obtain the Snap token which are explained in the table given below.

<Table>
  <thead>
    <tr>
      <th>
        Element
      </th>

      <th>
        Description
      </th>
    </tr>
  </thead>

  <tbody>
    <tr>
      <td>
        `Server Key`
      </td>

      <td>
        API server key. For more details, refer to [Retrieving API Access Keys](/docs/midtrans-account#retrieving-api-access-keys)
      </td>
    </tr>

    <tr>
      <td>
        `order_id`
      </td>

      <td>
        Unique transaction order ID, defined from your side. One ID could be used only once for the order of the material. Allowed character are Alphanumeric, dash(-), underscore(\_), tilde (\~), and dot (.) String, max 50.
      </td>
    </tr>

    <tr>
      <td>
        `gross_amount`
      </td>

      <td>
        Total amount of transaction, defined from your side. Integer.

        When creating 1st transaction, the gross\_amount is still required.\
        Although, then the actual accepted payment amount is allowed to be different that the initial gross\_amount, so customer can flexibly pay with any acceptable amount.
      </td>
    </tr>
  </tbody>
</Table>

<br />

## Charge API Sample Request

<br />

#### Endpoints

| Environment | Method | URL                                                     |
| ----------- | ------ | ------------------------------------------------------- |
| Sandbox     | POST   | `https://app.sandbox.midtrans.com/snap/v1/transactions` |
| Production  | POST   | `https://app.midtrans.com/snap/v1/transactions`         |

<br />

### HTTP Headers

<br />

|               |                     |
| :------------ | :------------------ |
| Accept        | `application/json`  |
| Content-Type  | `application/json`  |
| Authorization | `Basic AUTH_STRING` |

<br />

**AUTH\_STRING**: Base64Encode(`"YourServerKey"+":"`)

<br />

> 📘
>
> Midtrans API validates HTTP request by using Basic Authentication method. The username is your **Server Key** while the password is empty. The authorization header value is represented by AUTH\_STRING. AUTH\_STRING is base-64 encoded string of your username and password separated by colon symbol (**:**). For more details, refer to [ API Authorization and Headers](/docs/api-authorization-headers).

<br />

### Sample Request to Obtain Transaction Token

<br />

```curl
curl -X POST \
  https://app.sandbox.midtrans.com/snap/v1/transactions \
  -H 'Accept: application/json'\
  -H 'Authorization: Basic U0ItTWlkLXNlcnZlci1UT3ExYTJBVnVpeWhoT2p2ZnMzVV7LZU87' \
  -H 'Content-Type: application/json' \
  -d '{
    "transaction_details": {
        "order_id": "YOUR-ORDERID-123456",
        "gross_amount": 10000
    },
    "credit_card":{
        "secure" : true
    },
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
                              
Alternatively, if you are not using **Composer**, you can download midtrans-php library 
(https://github.com/Midtrans/midtrans-php/archive/master.zip), and then require 
the file manually.   

require_once dirname(__FILE__) . '/pathofproject/Midtrans.php'; */

//SAMPLE REQUEST START HERE

// Set your Merchant Server Key
\Midtrans\Config::$serverKey = 'YOUR_SERVER_KEY';
// Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
\Midtrans\Config::$isProduction = false;
// Set sanitization on (default)
\Midtrans\Config::$isSanitized = true;
// Set 3DS transaction for credit card to true
\Midtrans\Config::$is3ds = true;

$params = array(
    'transaction_details' => array(
        'order_id' => rand(),
        'gross_amount' => 10000,
    ),
    'customer_details' => array(
        'first_name' => 'budi',
        'last_name' => 'pratama',
        'email' => 'budi.pra@example.com',
        'phone' => '08111222333',
    ),
);

$snapToken = \Midtrans\Snap::getSnapToken($params);
```

```javascript NodeJS
/*Install midtrans-client (https://github.com/Midtrans/midtrans-nodejs-client) NPM package.
npm install --save midtrans-client*/

//SAMPLE REQUEST START HERE

const midtransClient = require('midtrans-client');
// Create Snap API instance
let snap = new midtransClient.Snap({
        // Set to true if you want Production Environment (accept real transaction).
        isProduction : false,
        serverKey : 'YOUR_SERVER_KEY'
    });

let parameter = {
    "transaction_details": {
        "order_id": "YOUR-ORDERID-123456",
        "gross_amount": 10000
    },
    "credit_card":{
        "secure" : true
    },
    "customer_details": {
        "first_name": "budi",
        "last_name": "pratama",
        "email": "budi.pra@example.com",
        "phone": "08111222333"
    }
};

snap.createTransaction(parameter)
    .then((transaction)=>{
        // transaction token
        let transactionToken = transaction.token;
        console.log('transactionToken:',transactionToken);
    })
```

```java
/*Install midtrans-java (https://github.com/Midtrans/midtrans-java) library.
If you are using Maven as the build automation tool for your project,
please add the following dependency to your project's build definition (pom.xml).
<dependencies>
    <dependency>
      <groupId>com.midtrans</groupId>
      <artifactId>java-library</artifactId>
      <version>3.0.0</version>
    </dependency>
</dependencies>
  
If you are using Gradle as the build tool for your project, 
please add the following dependency to your project's build definition (build.gradle).

dependencies {
    implementation 'com.midtrans:java-library:3.0.0'
}

You can also check the functional test 
(https://github.com/Midtrans/midtrans-java/blob/master/library/src/test/java/com/midtrans/java/CoreApiTest.java) for more examples.
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

      // Create params JSON Raw Object request
      public Map<String, Object> requestBody() {
          UUID idRand = UUID.randomUUID();
          Map<String, Object> params = new HashMap<>();

          Map<String, String> transactionDetails = new HashMap<>();
          transactionDetails.put("order_id", idRand);
          transactionDetails.put("gross_amount", "265000");

          Map<String, String> creditCard = new HashMap<>();
          creditCard.put("secure", "true");

          params.put("transaction_details", transactionDetails);
          params.put("credit_card", creditCard);

          return params;
      }

      // Create Token and then you can send token variable to FrontEnd,
      // to initialize Snap JS when customer click pay button
      String transactionToken = SnapApi.createTransactionToken(requestBody())
    }
}

```

```python
#Install midtransclient(https://github.com/Midtrans/midtrans-python-client) PIP package
#pip install midtransclient

#SAMPLE REQUEST START HERE

import midtransclient
# Create Snap API instance
snap = midtransclient.Snap(
    # Set to true if you want Production Environment (accept real transaction).
    is_production=False,
    server_key='YOUR_SERVER_KEY'
)
# Build API parameter
param = {
    "transaction_details": {
        "order_id": "test-transaction-123",
        "gross_amount": 200000
    }, "credit_card":{
        "secure" : True
    }, "customer_details":{
        "first_name": "budi",
        "last_name": "pratama",
        "email": "budi.pra@example.com",
        "phone": "08111222333"
    }
}

transaction = snap.create_transaction(param)

transaction_token = transaction['token']
```

```go
/*Import Midtrans Go module package to your project. Via terminal:
go get -u github.com/midtrans/midtrans-go

and/or on your project code:
import (
    "github.com/midtrans/midtrans-go"
    "github.com/midtrans/midtrans-go/coreapi"
    "github.com/midtrans/midtrans-go/snap"
    "github.com/midtrans/midtrans-go/iris"
)*/

//SAMPLE REQUEST START HERE

// 1. Initiate Snap client
var s = snap.Client
s.New("YOUR-SERVER-KEY", midtrans.Sandbox)
// Use to midtrans.Production if you want Production Environment (accept real transaction).

// 2. Initiate Snap request param
req := & snap.RequestParam{
    TransactionDetails: midtrans.TransactionDetails{
      OrderID:  "YOUR-ORDER-ID-12345",
      GrossAmt: 100000,
    }, 
    CreditCard: &snap.CreditCardDetails{
      Secure: true,
    },
    CustomerDetail: &midtrans.CustomerDetails{
      FName: "John",
      LName: "Doe",
      Email: "john@doe.com",
      Phone: "081234567890",
    },
  }

// 3. Execute request create Snap transaction to Midtrans Snap API
snapResp, _ := s.CreateTransaction(req)
```

<details>
  <summary><b>Using Postman</b></summary>

  <article>
    <br />

    Postman is an API development tool which is used to build, test and modify APIs. You can view our Postman Collection with the steps given below.

    1. Download and open [Postman](https://www.getpostman.com).
    2. Use this button to import our Postman Collection.

    [![Run in Postman](https://run.pstmn.io/button.svg)](https://app.getpostman.com/run-collection/af068be08b5d1a422796)

    3. Navigate to `1.a.  SNAP transaction token request (minimum)`.
    4. For more details, refer to [Postman Collection](/docs/midtrans-api-postman-collection).
  </article>
</details>

<br />

Please check Midtrans [available **language libraries**](/docs/midtrans-api-libraries-plugins) for other language samples.

<br />

> 📘 Tips
>
> You can [include more information](/docs/snap-advanced-feature#recommended-parameters) such as `customer_details`, `item_details`, and so on. It is recommended to send more details regarding the transaction, so that these details will be captured on the transaction record. Which can be [viewed on the Midtrans Dashboard](/docs/midtrans-dashboard-usage).

<br />

Learn more on why this API request [should be securely managed from your backend](/docs/payment-security#sensitive-data-encryption).

<br />

### Successful Sample Response

<br />

```json
{
  "token":"66e4fa55-fdac-4ef9-91b5-733b97d1b862",
  "redirect_url":"https://app.sandbox.midtrans.com/snap/v2/vtweb/66e4fa55-fdac-4ef9-91b5-733b97d1b862"
}
```

<br />

## Status Codes and Errors

<br />

| Status Code | Description                                                                                                             | Example                                                                       |
| ----------- | ----------------------------------------------------------------------------------------------------------------------- | ----------------------------------------------------------------------------- |
| 201         | Successful creation of Snap token.                                                                                      | "token":"66e4fa55-fdac-4ef9-91b5-733b97d1b862"                                |
| 401         | Failed to create a token, as wrong authorization is sent.                                                               | "Access denied, please check client or server key"                            |
| 4xx         | Failed to create a token, as wrong parameter is sent. Follow the error\_message and check your parameter.               | "transaction\_details.gross\_amount is not equal to the sum of item\_details" |
| 5xx         | Failed to create a token, because of  Midtrans internal error. Most of the time this is temporary, you can retry later. | "Sorry, we encountered internal server error. We will fix this soon."         |

<br />

***

<br />

# 2. Displaying Snap Payment Page on Frontend

<br />

To display Snap payment page within your site, there are two methods to choose.

<br />

## Redirection Method

<br />

You can use `redirect_url` retrieved from backend in the previous step to redirect customer to url hosted by Midtrans. Useful if you do not want (or unable) to implement via snap.js. [Learn more in this alternative section](#alternative-way-to-display-snap-payment-page-via-redirect).

You can also [use WebView to display Snap payment page](#display-snap-via-mobile-apps-webview) within your mobile app - also useful if you're looking to implement in cross platform applications (e.g Flutter or React) or native applications (SDK method is also available and recommended for native app - see guide [here](/reference/mobile-sdk-overview)).

<br />

## Javascript Method

<br />

> 📘 Tips
>
> * If you are using frontend framework such as ReactJS and struggling to include the script tag, please [refer to this recommendation](/docs/technical-faq#my-developer-uses-react-js-frontend-framework-and-is-unable-to-use-midtransminjssnapjs-what-should-i-do).
> * To ensure that Snap modal is displayed correctly on a mobile device, please include the viewport meta tag inside your `<head>` tag. The most common implementation is as follows :\
>   `<meta name="viewport" content="width=device-width, initial-scale=1">`
> * Optionally, you can also use [JavaScript callbacks](/docs/snap-advanced-feature#javascript-callback) to handle payment events triggered from customer finishing interaction with Snap payment page on frontend.

<br />

Include `snap.js` library into your payment page HTML. The table given below describes the components which are required to display Snap payment page.

<br />

| Element             | Description                                                                                                                 |
| ------------------- | --------------------------------------------------------------------------------------------------------------------------- |
| Client Key          | The *Client Key*. For more details refer to [Retrieving API Access Keys](/docs/midtrans-account#retrieving-api-access-keys) |
| `snap.js` url       | `https://app.sandbox.midtrans.com/snap/snap.js`                                                                             |
| transaction `token` | Retrieved from backend in [previous step](#_1-acquiring-transaction-token-on-backend)                                       |

<br />

Enter your *Client Key* as the value of `data-client-key` attribute in snap.js script tag.

<br />

There are two ways to display Snap's UI modal in your web/app, first is by embedding Snap modal within your web/app's page (**Embedded mode**) or by displaying the modal as an overlay on your web/app's page (**Pop up mode**).  Refer to sample implementations of [Embedded mode](https://demo.sandbox.midtrans.com/) and [Pop up mode](https://demo.midtrans.com).

<br />

### Embedded Mode

<br />

You can embed Snap modal within your page using this method. See how it will look like below, or try the demo [here](https://demo.sandbox.midtrans.com).  Check also our [integration recipe](https://docs.midtrans.com/recipes/snap-how-to-embed-snap-ui) for step by step implementation walkthrough.

<br />

<Image alt="Snap Embedded" align="center" width="% " src="https://files.readme.io/9b87685-Snap_Embedded.gif">
  Snap Embedded - Web Implementation
</Image>

<br />

<Image alt="Snap Embedded - Mobile Implementation via Mobile View" align="center" width="40% " src="https://files.readme.io/39c1768-Snap_embedded_-_mobile.gif">
  Snap Embedded - Mobile Implementation via Mobile View
</Image>

<br />

#### Embed Snap Modal

<br />

Steps to implement :

1. Create an empty div with the desired ID, e.g. `<div id="snap-container"></div>`. This div is where the SNAP application will be placed.
2. Add **snap.embed('$\{snap-token}', \{ embedId: $\{your div id, for example snap-container} })**. This will ensure that SNAP is correctly embedded and rendered within the div you previously set up.

<br />

```html Basic
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- @TODO: replace SET_YOUR_CLIENT_KEY_HERE with your client key -->
  <script type="text/javascript"
		src="https://app.stg.midtrans.com/snap/snap.js"
    data-client-key="SET_YOUR_CLIENT_KEY_HERE"></script>
  <!-- Note: replace with src="https://app.midtrans.com/snap/snap.js" for Production environment -->
</head>

<body>
  <button id="pay-button">Pay!</button>

  <!-- @TODO: You can add the desired ID as a reference for the embedId parameter. -->
  <div id="snap-container"></div>

  <script type="text/javascript">
    // For example trigger on button clicked, or any time you need
    var payButton = document.getElementById('pay-button');
    payButton.addEventListener('click', function () {
      // Trigger snap popup. @TODO: Replace TRANSACTION_TOKEN_HERE with your transaction token.
      // Also, use the embedId that you defined in the div above, here.
      window.snap.embed('YOUR_SNAP_TOKEN', {
        embedId: 'snap-container'
      });
    });
  </script>
</body>

</html>
```

```html With JS Callback
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- @TODO: replace SET_YOUR_CLIENT_KEY_HERE with your client key -->
  <script type="text/javascript"
		src="https://app.stg.midtrans.com/snap/snap.js"
    data-client-key="SET_YOUR_CLIENT_KEY_HERE"></script>
  <!-- Note: replace with src="https://app.midtrans.com/snap/snap.js" for Production environment -->
</head>

<body>
  <button id="pay-button">Pay!</button>

  <!-- @TODO: You can add the desired ID as a reference for the embedId parameter. -->
  <div id="snap-container"></div>

  <script type="text/javascript">
    // For example trigger on button clicked, or any time you need
    var payButton = document.getElementById('pay-button');
    payButton.addEventListener('click', function () {
      // Trigger snap popup. @TODO: Replace TRANSACTION_TOKEN_HERE with your transaction token.
      // Also, use the embedId that you defined in the div above, here.
      window.snap.embed('YOUR_SNAP_TOKEN', {
        embedId: 'snap-container',
        onSuccess: function (result) {
          /* You may add your own implementation here */
          alert("payment success!"); console.log(result);
        },
        onPending: function (result) {
          /* You may add your own implementation here */
          alert("wating your payment!"); console.log(result);
        },
        onError: function (result) {
          /* You may add your own implementation here */
          alert("payment failed!"); console.log(result);
        },
        onClose: function () {
          /* You may add your own implementation here */
          alert('you closed the popup without finishing the payment');
        }
      });
    });
  </script>
</body>

</html>
```

<br />

Learn more about [Snap's Javascript Callback here](/docs/snap-advanced-feature) and Snap's Javascript optional parameters [here.](/docs/snap-advanced-feature#snapjs-main-functions).

<br />

To make the modal blends more seamlessly to your website, is it possible to hide Snap's modal header that shows your merchant/display name. To do so, go to Dashboard > [Snap Preference](https://dashboard.midtrans.com/settings/snap_preference) > Theme and Logo > then untick `Use Header`.

<br />

#### Adjusting Snap Modal Dimension

<br />

* The default width and height are set to 320px and 560px, which are the minimum sizes that meet our application standards.
* Size of snap-container div can be adjusted by adding height and width via CSS. Note that Snap-container size can only be enlarged, but not make it smaller than the default width and height to ensure that customers can easily make payments and ensure all functionality works properly.
* We also ensure responsiveness by using flexbox, which sets the width to 100% and follows the flex behavior of its parent. This ensures that the Snap content is appropriately displayed and preserved on smaller devices.

<br />

![](https://files.readme.io/a713534-Snap_Flexbox.gif)

<br />

### Additional Implementation Notes

<br />

* Unlike in Pop Up mode; in Embedded mode, X button in the modal is intentionally removed to prevent users from accidentally exiting after making a payment. However, merchant can still close the Snap window by using the hide() method covered in Snap Pop Up guide. Learn more [here](/reference/snap-js).
* It is not possible to have two different types of Snap instances open simultaneously. If Snap Popup is currently active, Snap Embed cannot be displayed. To switch between the two methods, you will need to hide the active instance using the hide method.
* It is possible to hide the header section in Snap modal that shows your merchant/display name. To do so, go to Dashboard > [Snap Preference](https://dashboard.midtrans.com/settings/snap_preference) > Theme and Logo > then untick `Use Header`.

<br />

### Pop Up Mode

<br />

Display the Snap Checkout modal by overlaying it over your page (pop up). Start the payment process by calling `window.snap.pay` with transaction `token`.

<br />

```html Basic
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- @TODO: replace SET_YOUR_CLIENT_KEY_HERE with your client key -->
    <script type="text/javascript"
      src="https://app.sandbox.midtrans.com/snap/snap.js"
      data-client-key="SET_YOUR_CLIENT_KEY_HERE"></script>
    <!-- Note: replace with src="https://app.midtrans.com/snap/snap.js" for Production environment -->
  </head>

  <body>
    <button id="pay-button">Pay!</button>

    <script type="text/javascript">
      // For example trigger on button clicked, or any time you need
      var payButton = document.getElementById('pay-button');
      payButton.addEventListener('click', function () {
        // Trigger snap popup. @TODO: Replace TRANSACTION_TOKEN_HERE with your transaction token
        window.snap.pay('TRANSACTION_TOKEN_HERE');
        // customer will be redirected after completing payment pop-up
      });
    </script>
  </body>
</html>
```

```html With JS Callback
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- @TODO: replace SET_YOUR_CLIENT_KEY_HERE with your client key -->
    <script type="text/javascript"
      src="https://app.sandbox.midtrans.com/snap/snap.js"
      data-client-key="SET_YOUR_CLIENT_KEY_HERE"></script>
    <!-- Note: replace with src="https://app.midtrans.com/snap/snap.js" for Production environment -->
  </head>

  <body>
    <button id="pay-button">Pay!</button>

    <script type="text/javascript">
      // For example trigger on button clicked, or any time you need
      var payButton = document.getElementById('pay-button');
      payButton.addEventListener('click', function () {
        // Trigger snap popup. @TODO: Replace TRANSACTION_TOKEN_HERE with your transaction token
        window.snap.pay('TRANSACTION_TOKEN_HERE', {
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
      });
    </script>
  </body>
</html>
```

Learn more about [Snap's Javascript Callback here](/docs/snap-advanced-feature) and Snap's Javascript optional parameters [here.](/docs/snap-advanced-feature#snapjs-main-functions).

<br />

After following the steps given above, the sample Snap page is displayed as shown below.

<br />

![](https://files.readme.io/b8fc165-snap-popup-preview.gif "snap-popup-preview.gif")

<br />

Or try the demo [here](https://demo.midtrans.com).

After the payment is completed, customer is redirected back to `Finish URL`. It is specified on [Midtrans Dashboard](/docs/snap-advanced-feature#configuring-redirect-url), under menu **Settings > Snap Preference > System Settings >`Finish URL`** .

<br />

***

<br />

# 3. Updating Snap Preference

<br />

Make sure to update your [Snap Preference](https://dashboard.midtrans.com/settings/snap_preference) to customize and prepare your Snap Checkout integration, from your branding, active payment methods, payment expire time to callback URL.

<br />

![](https://files.readme.io/e60185b-image.png)

<br />

> 📘 Update Redirection Settings
>
> Make sure to at least update your Redirection Settings (Finish URL, Unfinish URL, Error URL) to make sure your customers will be redirected properly after finishing a payment. If you don't set it up here, you'll need to pass your Finish URLs as part of your Snap token creation requests.

<br />

***

<br />

# 4. Creating Test Payment

<br />

Create a test payment to make sure you have integrated Snap successfully. Following are the test credentials for Card payment.

<br />

| Name        | Value                                                |
| ----------- | ---------------------------------------------------- |
| Card Number | `4811 1111 1111 1114`                                |
| CVV         | `123`                                                |
| Exp Month   | Any month in MM format. For example, `02`            |
| Exp Year    | Any future year, in YYYY format. For example, `2025` |
| OTP/3DS     | `112233`                                             |

<br />

In addition to that, there are various payment methods available on Snap. You can choose any one of them to create a test payment. For more details, refer to [Testing Payments on Sandbox](/docs/testing-payment-on-sandbox).

![](https://files.readme.io/49f7af7-snap-test-transaction.gif "snap-test-transaction.gif")

<br />

***

<br />

# 5. Handling After Payment

<br />

When the transaction status changes, customer is redirected to *Redirect URL* and Midtrans sends HTTP notification to the merchant backend. This ensures that you are updated of the transaction status securely.

HTTP POST request with JSON body will be sent to your server's *Notification URL* configured on dashboard.

<br />

### Configuring Payment Notification URL

<br />

To configure the Payment Notification URL, follow the steps given below.

1. Login to your MAP account.
2. On the Home page, go to **SETTINGS > CONFIGURATION**.\
   *Configuration* page is displayed.
3. Enter **Payment Notification URL**.
4. Click **Update**.

<br />

![](https://files.readme.io/a366c5b-core-api-payment-notification-1.png "core-api-payment-notification-1.png")

<br />

The URL is updated and a confirmation message is displayed.

<br />

### HTTP(S) Notification/Webhooks

Learn more [here](/docs/https-notification-webhooks).

<br />

***

<br />

# Next Step

<br />

### Taking Action of Payment

Learn how to handle events of payment completed by customer and other status changes [here](/docs/handle-after-payment).

<br />

### Snap Advanced Feature

Learn the various useful features that are provided by Snap API [here](/docs/snap-advanced-feature).

<br />

### Transaction Status Cycle and Action

Learn how transaction status can change, and what are the available actions to take [here](/docs/transaction-status-cycle).

<br />

### Sample Codes

Integration sample codes are also available on our [GitHub repos](/docs/midtrans-api-libraries-plugins#sample-integration-code).

<br />

### Alternative way to Display Snap Payment Page via Redirect

Alternatively, you can also use `redirect_url` retrieved from backend in the [1st step](#sample-response) to redirect customer to payment page hosted by Midtrans. This is useful if you do not want or can not display payment page on your web page via snap.js.

Additionally, you can configure where customer will be redirected after the payment page, by: Login to your MAP/Midtrans Dashboard account, then go to **SETTINGS > CONFIGURATION**. Then please configure the Finish, Unfinish, Error Redirection URLs.

[Learn more here on configuring Snap Redirect url configuration](/docs/snap-advanced-feature#configuring-redirect-url), after clicking that link please choose the `Snap Redirect (Alternative)` tab.

<br />

<details>
  <summary><b>Configuring Finish Redirect URL</b></summary>

  <article>
    <br />

    To configure the Finish Redirect URL, follow the steps given below.

    1. Login to your MAP account.
    2. On the Home page, go to **SETTINGS > CONFIGURATION**.\
       *Configuration* page is displayed.
    3. Enter **Finish, Unfinish, and Error Redirect URL** with your landing page url.
    4. Click **Update**. A confirmation message is displayed.

    <br />

    ![](https://files.readme.io/cbd94a2-core-api-finish-redirect-url-2.png "core-api-finish-redirect-url-2.png")

    <br />

    The *Finish Redirect URL* is configured.
  </article>
</details>

<br />

### Display Snap via Mobile App’s WebView

Displaying Snap payment page within WebView can be a quick and easy way to get a payment page on your mobile app. Learn more about [Displaying Snap via WebView here](/docs/technical-faq#does-midtrans-support-flutter-react-native-or-other-hybridnon-native-mobile-framework).

To further minimize implementation, instead of implementing Snap pop-up via `snap.js`, you can use [Snap's `redirect_url`](#sample-response) to be displayed within the WebView.

You can check [demo of Snap displayed via WebView here](https://sample-demo-dot-midtrans-support-tools.et.r.appspot.com/snap-webview).

<br />

<br />

<br />

\-->