<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Payum\HeidelpayBundle\Extension;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use CoreShop\Component\Core\Model\PaymentInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Extension\Context;
use Payum\Core\Extension\ExtensionInterface;
use Payum\Core\Request\Convert;

final class PopulateHeidelpayExtension implements ExtensionInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param Context $context
     */
    public function onPostExecute(Context $context)
    {
        $action = $context->getAction();

        $previousActionClassName = get_class($action);
        if (false === stripos($previousActionClassName, 'ConvertPaymentAction')) {
            return;
        }

        /** @var Convert $request */
        $request = $context->getRequest();
        if (false === $request instanceof Convert) {
            return;
        }

        /** @var PaymentInterface $payment */
        $payment = $request->getSource();
        if (false === $payment instanceof PaymentInterface) {
            return;
        }

        /** @var OrderInterface $order */
        $order = $payment->getOrder();
        $gatewayLanguage = 'en';
        $customerData = [];

        if (!empty($order->getLocaleCode())) {
            $gatewayLanguage = $order->getLocaleCode();

            if (strpos($gatewayLanguage, '_') === true) {
                $gatewayLanguage = reset(explode('_', $gatewayLanguage));
            }

            /**
             * @var $customer CustomerInterface
             * @var $invoiceAddress AddressInterface
             */
            $customer = $order->getCustomer();
            $invoiceAddress = $order->getInvoiceAddress();

            $customerData = [
                'name' => $customer->getFirstname(),
                'lastname' => $customer->getLastname(),
                'company' => $invoiceAddress->getCompany(),
                'customer_id' => $customer->getId(),
                'street' => $invoiceAddress->getStreet(),
                'state' => $invoiceAddress->getState() ? $invoiceAddress->getState()->getName($gatewayLanguage) : '',
                'post_code' => $invoiceAddress->getPostcode(),
                'city' => $invoiceAddress->getCity(),
                'country_code' => $invoiceAddress->getCountry()->getIsoCode(),
                'email' => $customer->getEmail()
            ];
        }

        $result = ArrayObject::ensureArrayObject($request->getResult());
        $result = $result->toUnsafeArray();

        $result['language'] = $gatewayLanguage;
        $result['customer'] = $customerData;
        $result['basket']['amount'] = round($result['basket']['amount'] / 100, 2);

        $request->setResult((array)$result);

    }

    /**
     * {@inheritdoc}
     */
    public function onPreExecute(Context $context)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function onExecute(Context $context)
    {
    }
}
