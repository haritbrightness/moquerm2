<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */


namespace Amasty\Mostviewed\Plugin\SalesRule\Model;

use Amasty\Mostviewed\Api\Data\PackInterface;
use Amasty\Mostviewed\Model\OptionSource\DiscountType;
use Amasty\Mostviewed\Model\Pack;
use Magento\Quote\Model\Quote\Item\AbstractItem;

/**
 * Class RulesApplier
 * @package Amasty\Mostviewed\Plugin\SalesRule\Model
 */
class RulesApplier
{
    /**
     * @var AbstractItem
     */
    private $item;

    /**
     * @var array
     */
    protected $itemData;

    /**
     * @var null|array
     */
    private $productsInCart = null;

    /**
     * @var null|array
     */
    protected $productsQty = null;

    /**
     * @var \Magento\SalesRule\Model\Validator
     */
    private $validator;

    /**
     * @var \Amasty\Mostviewed\Api\PackRepositoryInterface
     */
    protected $packRepository;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    public function __construct(
        \Amasty\Mostviewed\Api\PackRepositoryInterface $packRepository,
        \Magento\SalesRule\Model\Validator $validator,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
    ) {
        $this->packRepository = $packRepository;
        $this->validator = $validator;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * @param \Magento\SalesRule\Model\RulesApplier $subject
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param \Magento\SalesRule\Model\ResourceModel\Rule\Collection $rules
     * @param bool $skipValidation
     * @param mixed $couponCode
     *
     * @return array
     */
    public function beforeApplyRules($subject, $item, $rules, $skipValidation, $couponCode)
    {
        $this->setItem($item);
        $this->itemData = [
            'itemPrice'         => $this->validator->getItemPrice($item),
            'baseItemPrice'     => $this->validator->getItemBasePrice($item),
            'itemOriginalPrice' => $this->validator->getItemOriginalPrice($item),
            'baseOriginalPrice' => $this->validator->getItemBaseOriginalPrice($item)
        ];

        return [$item, $rules, $skipValidation, $couponCode];
    }

    /**
     * @param \Magento\SalesRule\Model\RulesApplier $subject
     * @param array $appliedRuleIds
     *
     * @return array
     */
    public function afterApplyRules($subject, $appliedRuleIds)
    {
        $bundleRuleApplied = $this->checkForChilds();
        $bundleRuleApplied |= $this->checkForParents();
        if ($bundleRuleApplied) {
            $appliedRuleIds = [];
        }

        return $appliedRuleIds;
    }

    /**
     * @return array
     */
    protected function getProductsInCart()
    {
        if ($this->productsInCart === null) {
            $this->productsInCart = [];
            foreach ($this->getItem()->getAddress()->getAllItems() as $quoteItem) {
                $this->productsInCart[] = $quoteItem->getProductId();
                $this->productsQty[$quoteItem->getProductId()] = isset($this->productsQty[$quoteItem->getProductId()]) ?
                    $this->productsQty[$quoteItem->getProductId()] + $quoteItem->getTotalQty() :
                    $quoteItem->getTotalQty();
            }
        }

        return $this->productsInCart;
    }

    /**
     * @return bool
     */
    protected function checkForChilds()
    {
        $result = false;
        $packs = $this->packRepository->getPacksByChildProductsAndStore(
            [$this->getItem()->getProductId()],
            $this->getItem()->getStoreId()
        );
        if ($packs) {
            /** @var \Amasty\Mostviewed\Model\Pack $pack */
            foreach ($packs as $pack) {
                $parentProductsInCart = array_intersect(
                    $pack->getParentIds(),
                    $this->getProductsInCart()
                );

                $parentProductsInCart = array_diff($parentProductsInCart, [$this->getItem()->getProductId()]);
                if (!empty($parentProductsInCart)) {
                    $this->applyPackRule(
                        $pack,
                        $this->retrieveProductsQty($pack, $parentProductsInCart),
                        $pack->getChildProductQty($this->getItem()->getProductId())
                    );
                    $result = true;
                }
            }
        }

        return $result;
    }

    /**
     * @return bool
     */
    protected function checkForParents()
    {
        $result = false;
        $packs = $this->packRepository->getPacksByParentProductsAndStore(
            [$this->getItem()->getProductId()],
            $this->getItem()->getStoreId()
        );
        if ($packs) {
            /** @var \Amasty\Mostviewed\Model\Pack $pack */
            foreach ($packs as $pack) {
                if ($pack->getApplyForParent()) {
                    $childProductIds = array_intersect(
                        explode(',', $pack->getProductIds()),
                        $this->getProductsInCart()
                    );

                    $childProductIds = array_diff($childProductIds, [$this->getItem()->getProductId()]);
                    if (!empty($childProductIds)) {
                        $this->applyPackRule($pack, $this->retrieveProductsQty($pack, $childProductIds, true));
                        $result = true;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param \Amasty\Mostviewed\Model\Pack $pack
     * @param null|int $countCanApplied
     * @param int $minCount
     */
    protected function applyPackRule($pack, $countCanApplied = null, $minCount = 1)
    {
        $amount = 0;
        $baseAmount = 0;
        $qty = $countCanApplied && $countCanApplied < $this->getItem()->getTotalQty() ?
            $countCanApplied :
            $this->getItem()->getTotalQty();
        $multiplier = $qty - $qty % $minCount;
        switch ($pack->getDiscountType()) {
            case DiscountType::FIXED:
                $amount = $multiplier * $this->priceCurrency->convert(
                    $pack->getDiscountAmount(),
                    $this->getItem()->getQuote()->getStore()
                );
                $baseAmount = $multiplier * $pack->getDiscountAmount();
                break;
            case DiscountType::PERCENTAGE:
                $amount = $multiplier * $this->itemData['itemPrice'] *
                    $pack->getDiscountAmount() / 100;
                $baseAmount = $multiplier * $this->itemData['baseItemPrice'] *
                    $pack->getDiscountAmount() / 100;
                $amount = $this->priceCurrency->round($amount);
                $baseAmount = $this->priceCurrency->round($baseAmount);
                break;
        }
        $amount = min($amount, $multiplier * $this->itemData['itemPrice']);
        $baseAmount = min($baseAmount, $multiplier * $this->itemData['baseItemPrice']);
        $this->getItem()->setDiscountAmount($amount);
        $this->getItem()->setBaseDiscountAmount($baseAmount);
    }

    /**
     * @param Pack $pack
     * @param array $productIds
     * @param bool $forParent
     *
     * @return int
     */
    private function retrieveProductsQty($pack, $productIds, $forParent = false)
    {
        $productQty = 0;
        foreach ($productIds as $productId) {
            $productQty += $forParent
                ? floor($this->productsQty[$productId] / $pack->getChildProductQty($productId))
                : $this->productsQty[$productId] * $pack->getChildProductQty($this->getItem()->getProductId());
        }

        return $productQty;
    }

    /**
     * @param AbstractItem $item
     */
    public function setItem($item)
    {
        $this->item = $item;
    }

    /**
     * @return AbstractItem
     */
    public function getItem()
    {
        return $this->item;
    }
}
