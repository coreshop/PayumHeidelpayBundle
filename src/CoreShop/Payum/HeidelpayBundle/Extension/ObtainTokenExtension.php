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

use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use CoreShop\Component\Core\Model\PaymentInterface;
use Payum\Core\Extension\Context;
use Payum\Core\Extension\ExtensionInterface;
use Payum\Core\Request\Capture;
use Payum\Core\Request\RenderTemplate;

final class ObtainTokenExtension implements ExtensionInterface
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

    }

    /**
     * {@inheritdoc}
     */
    public function onPreExecute(Context $context)
    {
        $request = $context->getRequest();

        if (!$request instanceof RenderTemplate) {
            return;
        }

        $request->addParameter('layout', '@CoreShopFrontend/layout.html.twig');

        if (count($context->getPrevious()) === 0) {
            return;
        }

        $previous = reset($context->getPrevious());

        if (!$previous->getRequest() instanceof Capture) {
            return;
        }

        /** @var PaymentInterface $payment */
        $payment = $previous->getRequest()->getFirstModel();


        if (false === $payment instanceof PaymentInterface) {
            return;
        }

        /** @var OrderInterface $order */
        $order = $payment->getOrder();

        if (!$order instanceof OrderInterface) {
            return;
        }


        $request->addParameter('order', $order);
    }

    /**
     * {@inheritdoc}
     */
    public function onExecute(Context $context)
    {
    }
}
