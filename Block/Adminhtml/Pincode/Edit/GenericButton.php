<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Block\Adminhtml\Pincode\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\UrlInterface;

class GenericButton
{
    public function __construct(
        private readonly Context $context,
        private readonly UrlInterface $urlBuilder
    ) {}

    public function getPincodeId(): ?int
    {
        return $this->context->getRequest()->getParam('id') 
            ? (int)$this->context->getRequest()->getParam('id') 
            : null;
    }

    public function getUrl(string $route = '', array $params = []): string
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}
