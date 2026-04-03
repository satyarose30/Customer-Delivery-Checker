<?php
namespace Domus\CustomerDeliveryChecker\Block\Adminhtml\Pincode;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class BackButton implements ButtonProviderInterface
{
    public function getButtonData()
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()),
            'class' => 'back',
            'sort_order' => 10
        ];
    }

    public function getBackUrl()
    {
        return $this->getUrl('*/*/');
    }

    public function getUrl($route = '', $params = [])
    {
        return \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Backend\Model\UrlInterface::class)
            ->getUrl($route, $params);
    }
}
