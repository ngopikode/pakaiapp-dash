# IP Addresses & API Domain

## Notification IP Address

<br />

Midtrans sends (outgoing) [HTTP notifications / webhook](/docs/https-notification-webhooks) status update from various IP addresses to your notification url (as backend to backend request). In case your system needs to whitelist IP addresses, please add Midtrans CIDR given below to your whitelist.

### New IP Address

```
Production Environment 

8.215.30.222
147.139.209.49
8.215.32.142
147.139.163.77
8.215.25.24
8.215.3.193
147.139.210.20
149.129.238.95
8.215.9.206
147.139.134.22
149.129.253.222
8.215.56.174
8.215.27.65
147.139.129.139
149.129.192.10
8.215.15.117
149.129.234.6
8.215.79.106
149.129.192.204
8.215.83.17
147.139.197.147
147.139.207.105
147.139.193.191
147.139.201.222
8.215.82.175
149.129.218.45
8.215.10.140
8.215.83.130
147.139.206.209
8.215.75.234

Sandbox IP Environment

149.129.216.115
147.139.167.196
147.139.179.47
147.139.144.184
147.139.169.196
147.139.168.217
8.215.17.96
149.129.254.13
147.139.203.227
147.139.192.94
147.139.206.250
147.139.213.108
8.215.23.167
147.139.209.91
8.215.21.228
147.139.173.83
147.139.132.215
149.129.227.68
149.129.234.77
147.139.137.231
147.139.180.156
8.215.10.65
8.215.22.163
147.139.215.190
8.215.0.89
8.215.16.140
147.139.165.251
147.139.209.83
147.139.167.157
147.139.192.232
```

<br />

### Legacy IP Address

```
Production Environment:

103.208.23.0/24
103.208.23.6/32
103.127.16.0/23
103.127.17.6/32
34.87.92.33
34.87.59.67
35.186.147.251
34.87.157.231
13.228.166.126/32
52.220.80.5/32
3.1.123.95/32
108.136.204.114
108.136.34.95
108.137.159.245
108.137.135.225
16.78.53.66
43.218.2.230
16.78.88.149
16.78.85.64
16.78.69.49
16.78.98.130
16.78.9.40
43.218.223.26
13.228.166.126/32
52.220.80.5/32
3.1.123.95/32

Sandbox Environment : 

34.101.68.130
34.101.92.69
34.142.147.133/32
34.142.169.131/32
34.142.231.22/32
35.240.161.215/32
34.142.227.232/32
34.124.184.175/32
35.197.130.2/32
34.142.233.114/32
```

<br />

Although we are providing the IP list, we don’t quite recommend relying on IP whitelisting to ensure notification authenticity. Instead, we **strongly recommend you to verify the authenticity** by [implementing the methods explained here](/docs/https-notification-webhooks#verifying-notification-authenticity).

<br />

> 📘
>
> If you are unable to **receive notification from Midtrans**, please ensure that your infrastructure is allowing HTTPs connection from the above-mentioned IP addresses. Additionally try to [follow this troubleshooting section](/docs/https-notification-webhooks#suggestion-on-troubleshooting-http-notification-failures).

***

## API Domain Endpoint

> ⚠️ IMPORTANT: DO NOT WHITELIST DOMAIN IP ADDRESSES
>
> **Midtrans API** endpoints & URLs are **publicly accessible via the internet**, by default you **do not need to whitelist** anything on your side to **send API requests to Midtrans**.
>
> If your network or infrastructure requires whitelisting for outbound connections, you must whitelist the domain names listed below. Do not whitelist IP addresses, as this will cause integration failures.

Midtrans API infrastructure is distributed across multiple servers and protected by cloud-based security layers (including CDN and load balancers). **Our IP addresses change frequently and without notice** due to automatic scaling, maintenance, and infrastructure updates.

Whitelisting IP addresses will break your integration. You must whitelist our API domain names instead.

```Text Production Environment
api.midtrans.com
app.midtrans.com
merchants.midtrans.com
merchants-app.midtrans.com
```

```Text Sandbox Environment
api.sandbox.midtrans.com
app.sandbox.midtrans.com
merchants-app.sbx.midtrans.com
merchants.sbx.midtrans.com
simulator.sandbox.midtrans.com
```

Why You Should Not Whitelist IP Addresses?

* Midtrans does not provide static IP addresses for our domain resolution.
* IP addresses change automatically as part of our cloud infrastructure, load balancing, and security operations.
* Whitelisting IP addresses will result in unexpected service disruptions when our infrastructure scales or updates.

If your firewall or security policy requires IP-based whitelisting:

* Work with your network team to configure domain-based whitelisting (DNS resolution).
* If domain whitelisting is technically impossible in your environment, please contact Midtrans Support to discuss alternative solutions for enterprise customers.