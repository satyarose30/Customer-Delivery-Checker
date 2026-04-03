<?php
namespace Domus\CustomerDeliveryChecker\Block\Adminhtml\Pincode;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton implements ButtonProviderInterface
{
    public function getButtonData()
    {
        return [
            'label' => __('Delete Pincode'),
            'class' => 'delete',
            'on_click' => 'deleteConfirm(' . json_encode(__('Are you sure you want to delete this pincode?'))
                . ', ' . json_encode($this->getDeleteUrl()) . ')',
            'sort_order' => 20,
        ];
    }

    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['id' => $this->getId()]);
    }

    public function getUrl($route = '', $params = [])
    {
        return \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Backend\Model\UrlInterface::class)
            ->getUrl($route, $params);
    }

    public function getId()
    {
        return \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\App\RequestInterface::class)
            ->getParam('id');
    }
}
