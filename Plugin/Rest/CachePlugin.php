<?php
namespace Domus\CustomerDeliveryChecker\Plugin\Rest;

class CachePlugin
{
    public function aroundDispatch($subject, \Closure $proceed, $request)
    {
        return $proceed($request);
    }
}
