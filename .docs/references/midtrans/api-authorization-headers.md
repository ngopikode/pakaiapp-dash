# API Authorization & Headers

For backend based API request/call, Midtrans API requires HTTP(s) headers that will be explained below.

## Content-Type and Accept Header

<br />

Midtrans API uses JSON format for input and output, hence it is required to specify JSON as content-type & accept JSON as response. The header specification is as shown below.

* `Content-Type: application/json`
* `Accept: application/json`

<br />

***

<br />

## Authorization Header

<br />

The **Authorization header** is used by Midtrans API to identify *merchant ID* for initiating the request and also to process the request according to the authorization. The Authorization Header is developed from the [**Server Key**](/docs/midtrans-account#retrieving-api-access-keys) This is a safety feature to prevent any unauthorized users.

As analogy in physical world, it can be considered as "a key to your car", so that only you can access your car (and only your car can be accessed by you).

* For **Sandbox environment**, obtain Server Key in Sandbox Dashboard, menu: [Settings - Access Keys](https://dashboard.sandbox.midtrans.com/settings/config_info).
* For **Production environment**, obtain Server Key in Dashboard, menu: [Settings - Access Keys](https://dashboard.midtrans.com/settings/config_info).

<br />

> ❗️
>
> Access Keys are unique for every merchant. Server Keys are secret, please always **keep Server Key confidential**.

<br />

To generate `Authorization` header value, follow the steps given below.

<br />

1. Follow the format of [**Basic Authentication**](https://swagger.io/docs/specification/authentication/basic-authentication/). (example: `Username:Password`)
2. Username and password are separated by `:` character.
3. *Server Key* is used as `Username`, there is no password, so password is blank/empty string.

* For example, if your *Server Key* is `SB-Mid-server-abc123cde456`, then `Username:Password` would be `SB-Mid-server-abc123cde456:`.

4. Encode this value into base64 format.

* For example, base64 of `SB-Mid-server-abc123cde456:` is `U0ItTWlkLXNlcnZlci1hYmMxMjNjZGU0NTY6`.

5. Add the word `Basic ` as prefix.

* The above value would be `Basic U0ItTWlkLXNlcnZlci1hYmMxMjNjZGU0NTY6`.

6. Your Authorization header is ready.

* `Authorization: Basic U0ItTWlkLXNlcnZlci1hYmMxMjNjZGU0NTY6`

<br />

Check out our tool to try out [Authorization Header calculation](https://jsfiddle.net/wx3hbcen/embedded/result,html/dark).

<br />

***

<br />

## Complete HTTP(s) Headers

<br />

| HTTP(s) Header  | Type                         | Description                                                                                                  |
| --------------- | ---------------------------- | ------------------------------------------------------------------------------------------------------------ |
| `Content-Type`  | application/json             | It indicates that JSON format will be used in the request. Midtrans API accepts JSON requests.               |
| `Accept`        | application/json             | It indicates that JSON format is acceptable as response for the request. Midtrans API responds back in JSON. |
| `Authorization` | base64Encode(Server Key+":") | The Authorization field in *Basic Auth* format, Server Key is used as username, and the password is blank.   |

### Sample Request

<br />

```curl
curl -X POST \
  https://app.sandbox.midtrans.com/snap/v1/transactions \
  -H 'Accept: application/json'\
  -H 'Authorization: Basic U0ItTWlkLXNlcnZlci1hYmMxMjNjZGU0NTY6' \
  -H 'Content-Type: application/json' \
  -d '{
    "transaction_details": {
        "order_id": "YOUR-ORDERID-123456",
        "gross_amount": 10000
    }
}'
```

<br />

### Exception on Frontend API Request

<br />

For API request from frontend/client side, such as GET Card Token API, the headers explained earlier **are not required**. To avoid the risk of exposing your *Server Key* on your publicly accessible frontend, you **should not** use *Server Key* to authorize the request. Instead, *Client Key* is used to authorize the HTTP(s) request.

<br />

Below is a sample request and explanation of the `/v2/token`endpoint.

<br />

| Key                  | Description                                 |
| -------------------- | ------------------------------------------- |
| HTTP(s) Method       | `GET`                                       |
| HTTP(s) Header       | -                                           |
| API endpoint url     | `https://api.sandbox.midtrans.com/v2/token` |
| Query Param for auth | `client_key=<YOUR-CLIENT-KEY>`              |

<br />

```curl
curl 'https://api.sandbox.midtrans.com/v2/token?client_key={YOUR-CLIENT-KEY}&card_cvv=123&gross_amount=20000&currency=IDR&card_number=4811111111111114&card_exp_month=02&card_exp_year=2025'
```

<br />