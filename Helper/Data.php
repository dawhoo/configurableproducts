<?php
namespace Dawhoo\ConfigurableProducts\Helper;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\CatalogInventory\Model\Spi\StockStateProviderInterface;
use Magento\ConfigurableProduct\Helper\Data as ConfigurableHelper;
use Magento\CatalogInventory\Api\StockRegistryInterface;
/**
 * Class Data
 * Helper class for getting options
 */
class Data extends ConfigurableHelper
{
    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistry;
    /**
     * @var StockStateProviderInterface
     */
    protected $stockStateProvider;
    /**
     * Data constructor.
     *
     * @param \Magento\Catalog\Helper\Image                        $imageHelper
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     */
    public function __construct(
        ImageHelper $imageHelper,
        StockRegistryInterface $stockRegistry,
        StockStateProviderInterface $stockStateProvider
    ){
        parent::__construct($imageHelper);
        $this->stockRegistry = $stockRegistry;
        $this->stockStateProvider = $stockStateProvider;
    }
    /**
     * Get Options for Configurable Product Options
     *
     * @param \Magento\Catalog\Model\Product $currentProduct
     * @param array $allowedProducts
     * @return array
     */
    public function getOptions($currentProduct, $allowedProducts)
    {
        $options = [];
        foreach ($allowedProducts as $product) {
            $productId = $product->getId();
            $stockItem = $this->stockRegistry->getStockItem($productId, $product->getStore()->getWebsiteId());
            if (!$stockItem->getIsInStock() || $stockItem->getQty() < 1) continue;
            $images = $this->getGalleryImages($product);
            if ($images) {
                foreach ($images as $image) {
                    $options['images'][$productId][] =
                        [
                            'thumb' => $image->getData('small_image_url'),
                            'img' => $image->getData('medium_image_url'),
                            'full' => $image->getData('large_image_url'),
                            'caption' => $image->getLabel(),
                            'position' => $image->getPosition(),
                            'isMain' => $image->getFile() == $product->getImage(),
                        ];
                }
            }
            foreach ($this->getAllowAttributes($currentProduct) as $attribute) {
                $productAttribute = $attribute->getProductAttribute();
                $productAttributeId = $productAttribute->getId();
                $attributeValue = $product->getData($productAttribute->getAttributeCode());
                $options[$productAttributeId][$attributeValue][] = $productId;
                $options['index'][$productId][$productAttributeId] = $attributeValue;
            }
        }
        return $options;
    }
}
