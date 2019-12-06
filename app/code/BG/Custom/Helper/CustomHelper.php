<?php
namespace BG\Custom\Helper;
use Magento\Framework\Registry;
use Magento\Catalog\Model\Category;

class CustomHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $category;
	public function __construct(
		Registry $registry,
        Category $category,
        \Magento\Framework\App\Helper\Context $context
    ) {
		$this->_registry = $registry;
        $this->category = $category;
        parent::__construct($context);
    }
    public function getCurrentProduct()
    {        
        return $this->_registry->registry('current_product');
    } 
    public function getSubCategories($categoryId)
    { 
        $allCatNames = array();
        $collection = $this->category->load($categoryId)->getChildrenCategories();
        foreach($collection as $names){
            $allCatNames[] = $names->getName();
        }
        return $allCatNames;
    }
}