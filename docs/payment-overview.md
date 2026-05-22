# Overview

Midtrans Payments Overview

Midtrans helps your business to accept payment methods such as [(credit & debit) card payment, bank transfer, e-Wallet, over the counter, cardless credits, and other methods](https://midtrans.com/payments).

Along with giving your customer freedom to pay with their favorite payment methods, Midtrans also offers you various integration options. You can pick the best suited option for your needs.

<br />

***

<br />

# <b>Integration Options</b>

## [Built-In Interface (Snap) for Your Web & App](/docs/snap)  <span style={{ color: "orange" }}><sup>Recommended</sup></span>

<br />

![](https://files.readme.io/4905529-Snap_Preview.png "Snap Preview.png")

<br />

Snap user interface helps to securely accept [payments ](https://midtrans.com/payments) on your website and mobile app with a few simple steps. Your customer is presented with a sleek, mobile-friendly interface to make payments that is optimized for payment conversion. Fastest way to integrate to Midtrans.

<br />

<p style={{ textAlign: "center" }}>
  <a href="https://demo.midtrans.com"><b>Preview Snap UI via Midtrans's Demo Store</b></a>
</p>

<br />

Try it yourself with this (less than 5 mins) [integration sample ↗](/docs/snap-interactive-demo)

<br />

> 📘 Tips
>
> Snap can also be embedded within your mobile app [using WebView](/docs/snap-snap-integration-guide#display-snap-via-mobile-apps-webview). Check [demo of Snap displayed in a WebView.](https://sample-demo-dot-midtrans-support-tools.et.r.appspot.com/snap-webview)

<br />

## [Native Mobile App SDK](/reference/mobile-sdk-overview)

<br />

![](https://files.readme.io/5da8e15-Native_Mobile_SDK.png "Native Mobile SDK.png")

<br />

Native Mobile App SDK helps you to accept payments within your mobile app. You can embed our Android and iOS Mobile SDK within your app. Similar to Snap, the Mobile SDK also provides drop-in user interface to accept payments using [Midtrans’s various payment methods](https://midtrans.com/payments).

<br />

<b>Try Mobile SDK via Simulator</b>

<br />

<Embed url="https://appetize.io/embed/9r0b89zu862f8eu1ukd0ecpgxc?device=pixel4&scale=71&orientation=portrait" title="Embed" favicon="https://appetize.io/favicon.ico" image="https://appetize.io/images/og/appetize-dark.png" provider="appetize.io" href="https://appetize.io/embed/9r0b89zu862f8eu1ukd0ecpgxc?device=pixel4&scale=71&orientation=portrait" height="600px" width="300px" iframe="true" />

<Embed url="https://appetize.io/embed/x4ace4dndczdbg1j633nq4cgbw?device=iphone11pro&scale=70&orientation=portrait" title="Embed" favicon="https://appetize.io/favicon.ico" image="https://appetize.io/images/og/appetize-dark.png" provider="appetize.io" href="https://appetize.io/embed/x4ace4dndczdbg1j633nq4cgbw?device=iphone11pro&scale=70&orientation=portrait" height="600px" width="300px" iframe="true" />

<br />

## [Custom Interface (Core API)](/docs/custom-interface-core-api)

<br />

Core API enables you to integrate Midtrans's Payment API directly to your own web/app in order to build your own payment-flow or use your own checkout interface. Core API can be integrated to website, web application, Point of Sales, IoT (Internet of Things) or any other internet-capable device. Core API uses REST API standard with JSON-based payload.

<br />

## [Payment Link](/docs/payment-link-overview)

<br />

Payment Link is a no code payment solutions to help you create a Midtrans's payment page and share the link to your customers from your dashboard.

Need to automate it? Create and send payment links programmatically via [API](/docs/payment-link-via-api) to invoice your customer.

<br />

## [CMS Payment Plugins](/docs/install-cms-plugins)

<br />

If you are already using e-commerce Content Management System (CMS) such as **Wordpress-Woocommerce, Magento, Prestashop, Opencart, WHMCS**, and so on, integrate it to Midtrans by installing Midtrans's payment plugin in a few simple steps.

<br />

## [Ecommerce Platform](https://docs.midtrans.com/docs/ecommerce-platform)

<br />

Accept payment in your online stores powered by ecommerce platforms such as **Shopify, Sirclo, Jejualan**, and so on. It is ready-to-use with little to no code needed, and requires minimal setup.

<br />

***

<br />

# <b>Comparison of Integration Options</b>

<br />

<HTMLBlock>
  {`
  <table>
    <thead>
      <tr>
        <th>Integration Type</th>
        <th>Features</th>
        <th>Sample Use Case</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong>Built-in Interface</strong> (Snap) </th>
        <td>
          <ul>
            <li>All-in-one payment UI that can display all available payment methods.</li>
            <li>Quick, integrate just one time, then any new payment methods can be auto added.</li>
            <li>Customizable payment methods, expiry time, & more via Dashboard/API.</li>
            <li>Customizable display name, brand logo, & theme color.</li>
          </ul>
        </td>
        <td>
          <ul>
            <li>Easy way to integrate payment quickly, & customizable. </li>
            <li>

  Embed payment page directly within your web (or mobile app, via <a href="https://docs.midtrans.com/docs/snap-snap-integration-guide#display-snap-via-mobile-apps-webview">webview</a>)</li>
            <li>Or redirect customer to Midtrans-hosted payment page.</li>
          </ul>
        </td>
      </tr>
      <tr>
        <td><strong>Native Mobile App SDK</strong></th>
        <td>
          <ul>
            <li>All the features of <strong>Snap</strong>, optimized for native Android & iOS app.</li>
            <li>Via importing Midtrans SDK within app codebase.</li>
          </ul>
        </td>
        <td>
          <ul>
            <li>For native mobile app based business (Android & iOS)</li>
            <li>Integrate quickly on mobile app, with a native performance & feels.</li>
          </ul>
        </td>
      </tr>
      <tr>
        <td><strong>Custom Interface</strong>(Core API)</th>
        <td>
          <ul>
            <li>Render and customize your own payment interface (UI).</li>
            <li>Customize interface for each payment methods.</li>
            <li>Advanced features like on-demand recurring charges.</li>
            <li>

  Compatible with web and non-web applications (hardware devices or custom software). Like <a href="#bother-options-to-integrateb">GoPay Integration for POS</a></li>
          </ul>
        </td>
        <td>
          <ul>
            <li>Fully customize the payment interface/UI, for each payment methods. </li>
            <li>Your business is PCI compliant.</li>
            <li>Custom integration like Vending Machine, POS, hardware devices, and so on. </li>
          </ul>
        </td>
      </tr>
      <tr>
        <td><strong>Payment Link</strong></th>
        <td>
          <ul>
            <li>No coding or programming needed.</li>
            <li>Create payment link from <strong>Dashboard</strong>.</li>
             <li>Payment link can be shared in your Blog, Social Media, Messenger, WhatsApp, Email, etc.</li>
            <li>Customizable number of payment (usage), URL part, & longer lifetime</li>
          </ul>
        </td>
        <td>
          <ul>
            <li>Send invoice to customer quickly, without complex integration. </li>
            <li>Sell on social media. </li>
            <li>Create once & use it for many customers, for products like tickets, virtual products, etc.</li>
          </ul>
        </td>
      </tr>
      <tr>
        <td><strong>Payment Link via API Integration</strong></th>
        <td>
          <ul>
            <li>Similar features & benefits as Payment Link, but easily integrated via API just like Snap.</li>
          </ul>
        </td>
        <td>
          <ul>
            <li>Can cover both use cases of Snap & Payment Link.</li>
          </ul>
        </td>
      </tr>
      <tr>
        <td><strong>CMS Payment Plugins</strong></th>
        <td>
          <ul>
            <li>No coding or programming needed.</li>
            <li>Simple to install & use.</li>
            <li>The features of  <strong>Snap</strong>, without complex integration.</li>
          </ul>
        </td>
        <td>
          <ul>
            <li>You are using CMS like WordPress, Magento, PrestaShop, WHMCS, etc.</li>
          </ul>
        </td>
      </tr>
      <tr>
        <td><strong>E-commerce Platform </strong></th>
        <td>
          <ul>
            <li>No coding or programming needed.</li>
            <li>Simple install & use, for third-party E-commerce platforms.</li>
            <li>The features of  <strong>Snap</strong>, without complex integration.</li>
          </ul>
        </td>
        <td>
          <ul>
            <li>You are using ready-to-use Platform like Shopify, Sirclo, Jejualan, etc.</li>
          </ul>
        </td>
      </tr>
    </tbody>
  </table>
  `}
</HTMLBlock>

<br />

> 📘 Note
>
> Those sample use case **does not limit** how you could fit the integration-type with your own unique requirement. You can get creative and go beyond those sample use case, and invent your own use case for that type of integration.

<br />

***

<br />

# <b>Other Options to Integrate</b>

<br />

* [GoPay Integration for POS](/docs/gopay-qris-pos-integration): The non-conventional web/app platforms (vending machine, TV box, IoT, point of sales, and so on) can be integrated with **Core API** as long as they are connected to the Internet.\
  These devices can easily start accepting payments using the API calls.
* If you are using non-native/hybrid mobile based app framework (such as React Native, Flutter, etc.) [you can try to follow this suggestion to integrate](/docs/technical-faq#does-midtrans-support-flutter-react-native-or-other-hybridnon-native-mobile-framework).

<br />

***

<br />

# <b>Next Step</b>

<br />

#### [Sign Up for Midtrans Account](/docs/midtrans-account)

Sign up for a Midtrans account to get your *Sandbox* API keys ready to test integration. To start accepting real payments, choose to complete registration in Midtrans's [dashboard](https://dashboard.midtrans.com) to activate payment methods in *Production* mode.