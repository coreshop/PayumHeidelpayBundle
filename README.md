# CoreShop Heidelpay Payum Connector
This Bundle activates the Heidelpay PaymentGateway in CoreShop.
It requires the [coreshop/payum-heidelpay](https://github.com/coreshop/payum-heidelpay) repository which will be installed automatically.

## Notice
The Heidelpay Payum Implementation currently only supports following gateways:
 - PayPal
 - Klarna Sofort
 - Credit Card

## Installation

#### 1. Composer
```json
    "coreshop/payum-heidelpay-bundle": "^1.0"
```

#### 2. Activate
Enable the Bundle in Pimcore Extension Manager

#### 3. Setup
Go to Coreshop -> PaymentProvider and add a new Provider. Choose `heidelpay` from `type` and fill out the required fields.

