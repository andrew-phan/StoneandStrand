<?php
/**
 * Plugin for \Magento\Catalog\Api\ProductRepositoryInterface
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ss\Designer\Plugin\UrlRewrite;

class UrlRewritePlugin
{
    /**
     * @var \Magento\UrlRewrite\Model\ResourceModel\UrlRewrite
     */
    protected $resourceModel;

    /**
     * @param \Magento\UrlRewrite\Model\ResourceModel\UrlRewrite $resourceModel
     */
    public function __construct(
        \Magento\UrlRewrite\Model\ResourceModel\UrlRewrite $resourceModel
    ) {
        $this->resourceModel = $resourceModel;
    }

    /**
     * @param \Ss\Designer\Helper\SsUrlRewriteProduct $subject
     * @param callable $proceed
     * @param type $urls
     * @return \Ss\Designer\Helper\SsUrlRewriteProduct
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundInsertMultiple(
        \Ss\Designer\Helper\SsUrlRewriteProduct $subject,
        \Closure $proceed,
        $urls
    ) {
        $this->resourceModel->beginTransaction();
        try {
            /** @var \Ss\Designer\Helper\SsUrlRewriteProduct $result */
            $result = $proceed($urls);
            $this->resourceModel->commit();
            return $result;
        } catch (\Exception $e) {
            $this->resourceModel->rollBack();
            throw $e;
        }
    }
}
