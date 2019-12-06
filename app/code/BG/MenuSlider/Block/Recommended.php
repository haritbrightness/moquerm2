<?php

namespace BG\MenuSlider\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\ObjectManagerInterface;
use Sm\MegaMenu\Helper\Defaults;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Url\DecoderInterface;
use Magento\Framework\Filter\Email;
use Magento\Catalog\Helper\Data;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Framework\View\Context as ViewContext;
use Sm\MegaMenu\Block\Cache\Lite;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Block\Product\ImageBuilder;
/**
 * Class Order
 */
class Recommended extends \Sm\MegaMenu\Block\MegaMenu\View
{
	protected $_productCollectionFactory;
	protected $_storeManager;
    protected $categoryRepository;
	protected $_imageBuilder;
    public function __construct(
        Template\Context $context,
        Defaults $defaults,
		AbstractProduct $abstractProduct,
		ObjectManagerInterface $objectManager,
		DecoderInterface $urlDecoder,
		Email $email,
		Data $catalogData,
		\Magento\Framework\Image\AdapterFactory $imageFactory,
		ViewContext $viewContext,
		array $data = [],
        CollectionFactory $productCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        ImageBuilder $imageBuilder
        
    ) {
    	$this->_productCollectionFactory = $productCollectionFactory;
    	$this->_storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
    	$this->_imageBuilder = $imageBuilder;
        parent::__construct($context, $defaults, $abstractProduct, $objectManager, $urlDecoder, $email, $catalogData, $imageFactory, $viewContext, $data);
    }
    public function getChildren($categoryId)
	{
		$category = $this->categoryRepository->get($categoryId, $this->_storeManager->getStore()->getId());
		return $category->getChildren();
		 // return $this->getCategory($categoryId)->getChildren();
	  //   if ($this->_categoryFactory) {
	  //       return $this->_categoryFactory->create()->getChildren();
	  //   } else {
	  //       return $this->getCategory($categoryId)->getChildren();
	  //   }        
	}    
    public function getProductCollection($categoryId){
    	$childListStr   = $this->getChildren($categoryId); // Provide the root category ID
	    $childList      = explode( ",", $childListStr );
	    $catToLoad      = array();

	    foreach( $childList as $item ){
	        array_push( $catToLoad, $item );
	    }
	    $collection = $this->_productCollectionFactory->create()->addAttributeToSelect('*')
						    ->addCategoriesFilter(['in' => $categoryId ])
						    //->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
						    ->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
	    // $collection->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);  
    	return $collection;

	    /*$category = $this->_categoryFactory->create()->load($categoryId);
	    $collection = $this->_productCollectionFactory->create();
	    $collection->addAttributeToSelect('*');
	    $collection->addCategoriesFilter(['in'=>$categoryId]);
	    // $collection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
	    // $collection->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
	    return $collection;*/
    }
    public function getImage($product){
    	 return $this->_imageBuilder->setProduct($product)
							       ->setImageId('category_page_list')
							       ->create();
    }
}