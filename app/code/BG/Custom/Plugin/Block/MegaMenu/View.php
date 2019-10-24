<?php

namespace BG\Custom\Plugin\Block\MegaMenu;
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
class View extends \Sm\MegaMenu\Block\MegaMenu\View 
{
	protected $scopeConfig;
	const XML_PATH_MEGAMENU_GROUPID = 'megamenu/general/group_id';

	/**
	 * @var \Magento\Framework\ObjectManagerInterface
	 */
	protected $_objectManager;
	protected $_defaults = null;

	/**
	 * @var \Magento\Framework\Url\DecoderInterface
	 */
	protected $_urlDecoder;
	protected $_product;

	/**
	 * @var \Magento\Framework\Filesystem
	 */
	protected $_directory;

	/**
	 * Content data
	 *
	 * @var Data
	 */
	protected $_contentData = null;

	/**
	 * Front controller
	 *
	 * @var \Magento\Framework\App\FrontControllerInterface
	 */
	protected $_frontController;
	protected $_filter;
	protected $_imageFactory;
	protected $_urlinterface = null;

	/**
	 * Symbol convert table
	 *
	 * @var convertTable
	 */
	protected $_convertTable = [
		'&amp;' => 'and',   '@' => 'at',    '©' => 'c', '®' => 'r', 'À' => 'a',
		'Á' => 'a', 'Â' => 'a', 'Ä' => 'a', 'Å' => 'a', 'Æ' => 'ae','Ç' => 'c',
		'È' => 'e', 'É' => 'e', 'Ë' => 'e', 'Ì' => 'i', 'Í' => 'i', 'Î' => 'i',
		'Ï' => 'i', 'Ò' => 'o', 'Ó' => 'o', 'Ô' => 'o', 'Õ' => 'o', 'Ö' => 'o',
		'Ø' => 'o', 'Ù' => 'u', 'Ú' => 'u', 'Û' => 'u', 'Ü' => 'u', 'Ý' => 'y',
		'ß' => 'ss','à' => 'a', 'á' => 'a', 'â' => 'a', 'ä' => 'a', 'å' => 'a',
		'æ' => 'ae','ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
		'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ò' => 'o', 'ó' => 'o',
		'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u',
		'û' => 'u', 'ü' => 'u', 'ý' => 'y', 'þ' => 'p', 'ÿ' => 'y', 'Ā' => 'a',
		'ā' => 'a', 'Ă' => 'a', 'ă' => 'a', 'Ą' => 'a', 'ą' => 'a', 'Ć' => 'c',
		'ć' => 'c', 'Ĉ' => 'c', 'ĉ' => 'c', 'Ċ' => 'c', 'ċ' => 'c', 'Č' => 'c',
		'č' => 'c', 'Ď' => 'd', 'ď' => 'd', 'Đ' => 'd', 'đ' => 'd', 'Ē' => 'e',
		'ē' => 'e', 'Ĕ' => 'e', 'ĕ' => 'e', 'Ė' => 'e', 'ė' => 'e', 'Ę' => 'e',
		'ę' => 'e', 'Ě' => 'e', 'ě' => 'e', 'Ĝ' => 'g', 'ĝ' => 'g', 'Ğ' => 'g',
		'ğ' => 'g', 'Ġ' => 'g', 'ġ' => 'g', 'Ģ' => 'g', 'ģ' => 'g', 'Ĥ' => 'h',
		'ĥ' => 'h', 'Ħ' => 'h', 'ħ' => 'h', 'Ĩ' => 'i', 'ĩ' => 'i', 'Ī' => 'i',
		'ī' => 'i', 'Ĭ' => 'i', 'ĭ' => 'i', 'Į' => 'i', 'į' => 'i', 'İ' => 'i',
		'ı' => 'i', 'Ĳ' => 'ij','ĳ' => 'ij','Ĵ' => 'j', 'ĵ' => 'j', 'Ķ' => 'k',
		'ķ' => 'k', 'ĸ' => 'k', 'Ĺ' => 'l', 'ĺ' => 'l', 'Ļ' => 'l', 'ļ' => 'l',
		'Ľ' => 'l', 'ľ' => 'l', 'Ŀ' => 'l', 'ŀ' => 'l', 'Ł' => 'l', 'ł' => 'l',
		'Ń' => 'n', 'ń' => 'n', 'Ņ' => 'n', 'ņ' => 'n', 'Ň' => 'n', 'ň' => 'n',
		'ŉ' => 'n', 'Ŋ' => 'n', 'ŋ' => 'n', 'Ō' => 'o', 'ō' => 'o', 'Ŏ' => 'o',
		'ŏ' => 'o', 'Ő' => 'o', 'ő' => 'o', 'Œ' => 'oe','œ' => 'oe','Ŕ' => 'r',
		'ŕ' => 'r', 'Ŗ' => 'r', 'ŗ' => 'r', 'Ř' => 'r', 'ř' => 'r', 'Ś' => 's',
		'ś' => 's', 'Ŝ' => 's', 'ŝ' => 's', 'Ş' => 's', 'ş' => 's', 'Š' => 's',
		'š' => 's', 'Ţ' => 't', 'ţ' => 't', 'Ť' => 't', 'ť' => 't', 'Ŧ' => 't',
		'ŧ' => 't', 'Ũ' => 'u', 'ũ' => 'u', 'Ū' => 'u', 'ū' => 'u', 'Ŭ' => 'u',
		'ŭ' => 'u', 'Ů' => 'u', 'ů' => 'u', 'Ű' => 'u', 'ű' => 'u', 'Ų' => 'u',
		'ų' => 'u', 'Ŵ' => 'w', 'ŵ' => 'w', 'Ŷ' => 'y', 'ŷ' => 'y', 'Ÿ' => 'y',
		'Ź' => 'z', 'ź' => 'z', 'Ż' => 'z', 'ż' => 'z', 'Ž' => 'z', 'ž' => 'z',
		'ſ' => 'z', 'Ə' => 'e', 'ƒ' => 'f', 'Ơ' => 'o', 'ơ' => 'o', 'Ư' => 'u',
		'ư' => 'u', 'Ǎ' => 'a', 'ǎ' => 'a', 'Ǐ' => 'i', 'ǐ' => 'i', 'Ǒ' => 'o',
		'ǒ' => 'o', 'Ǔ' => 'u', 'ǔ' => 'u', 'Ǖ' => 'u', 'ǖ' => 'u', 'Ǘ' => 'u',
		'ǘ' => 'u', 'Ǚ' => 'u', 'ǚ' => 'u', 'Ǜ' => 'u', 'ǜ' => 'u', 'Ǻ' => 'a',
		'ǻ' => 'a', 'Ǽ' => 'ae','ǽ' => 'ae','Ǿ' => 'o', 'ǿ' => 'o', 'ə' => 'e',
		'Ё' => 'jo','Є' => 'e', 'І' => 'i', 'Ї' => 'i', 'А' => 'a', 'Б' => 'b',
		'В' => 'v', 'Г' => 'g', 'Д' => 'd', 'Е' => 'e', 'Ж' => 'zh','З' => 'z',
		'И' => 'i', 'Й' => 'j', 'К' => 'k', 'Л' => 'l', 'М' => 'm', 'Н' => 'n',
		'О' => 'o', 'П' => 'p', 'Р' => 'r', 'С' => 's', 'Т' => 't', 'У' => 'u',
		'Ф' => 'f', 'Х' => 'h', 'Ц' => 'c', 'Ч' => 'ch','Ш' => 'sh','Щ' => 'sch',
		'Ъ' => '-', 'Ы' => 'y', 'Ь' => '-', 'Э' => 'je','Ю' => 'ju','Я' => 'ja',
		'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e',
		'ж' => 'zh','з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l',
		'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's',
		'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
		'ш' => 'sh','щ' => 'sch','ъ' => '-','ы' => 'y', 'ь' => '-', 'э' => 'je',
		'ю' => 'ju','я' => 'ja','ё' => 'jo','є' => 'e', 'і' => 'i', 'ї' => 'i',
		'Ґ' => 'g', 'ґ' => 'g', 'א' => 'a', 'ב' => 'b', 'ג' => 'g', 'ד' => 'd',
		'ה' => 'h', 'ו' => 'v', 'ז' => 'z', 'ח' => 'h', 'ט' => 't', 'י' => 'i',
		'ך' => 'k', 'כ' => 'k', 'ל' => 'l', 'ם' => 'm', 'מ' => 'm', 'ן' => 'n',
		'נ' => 'n', 'ס' => 's', 'ע' => 'e', 'ף' => 'p', 'פ' => 'p', 'ץ' => 'C',
		'צ' => 'c', 'ק' => 'q', 'ר' => 'r', 'ש' => 'w', 'ת' => 't', '™' => 'tm',
	];

	protected $_allLeafId;
	protected $_typeCurrentUrl = null;
	protected $_itemCurrentUrl = null;
	protected $_allItemsFirstColumnId = null;

	protected $_menuItem = null;
	protected $_modelProducts = null;
	protected $_modelCategory = null;

	const EXTERNALLINK = \Sm\MegaMenu\Model\Config\Source\Type::EXTERNALLINK;
	const PRODUCT = \Sm\MegaMenu\Model\Config\Source\Type::PRODUCT;
	const CATEGORY = \Sm\MegaMenu\Model\Config\Source\Type::CATEGORY;
	const CMSBLOCK = \Sm\MegaMenu\Model\Config\Source\Type::CMSBLOCK;

	const CMSPAGE = \Sm\MegaMenu\Model\Config\Source\Type::CMSPAGE;
	const CONTENT = \Sm\MegaMenu\Model\Config\Source\Type::CONTENT;
	const STATUS_ENABLED = \Sm\MegaMenu\Model\Config\Source\Status::STATUS_ENABLED;
	const PREFIX = \Sm\MegaMenu\Model\Config\Source\Html::PREFIX;
	const PAGE_MODULE = \Sm\MegaMenu\Model\Config\Source\Type::PAGE_MODULE;

	public function __construct(
		Context $context,
		Defaults $defaults,
		AbstractProduct $abstractProduct,
		ObjectManagerInterface $objectManager,
		DecoderInterface $urlDecoder,
		Email $email,
		Data $catalogData,
		\Magento\Framework\Image\AdapterFactory $imageFactory,
		ViewContext $viewContext,
		array $data = [],
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
	)
	{
		parent::__construct($context, $defaults, $abstractProduct, $objectManager, $urlDecoder, $email, $catalogData, $imageFactory, $viewContext, $data);
		$this->_objectManager = $objectManager;
		$this->_defaults = $defaults->get($data);
		$this->_urlDecoder = $urlDecoder;
		$this->_product = $abstractProduct;
		$this->_directory = $this->_objectManager->get('\Magento\Framework\Filesystem');
		$this->_contentData = $catalogData;
		$this->_frontController = $viewContext;
		$this->_filter = $email;
		$this->_imageFactory = $imageFactory;
		$this->_urlinterface = $this->_objectManager->get('\Magento\Framework\UrlInterface');
		if(!$this->_defaults['isenabled'] || !$this->_defaults['group_id']) return;
		$this->_menuItem = $this->createMenuItems();
		$this->_modelProducts = $this->_objectManager->create('Magento\Catalog\Model\Product');
		$this->_modelCategory = $this->_objectManager->create('Magento\Catalog\Model\Category');
		$itemsLeaf = $this->_menuItem->getAllLeafByGroupId($this->_defaults['group_id']);
		$itemsids = $this->getAllItemsIds($itemsLeaf);
		$this->_allLeafId = ($itemsLeaf)?$itemsids:'';
		if(!$this->_allItemsFirstColumnId){
			$itemsFirstColumn = $this->_menuItem->getAllItemsFirstByGroupId($this->_defaults['group_id']);
			$itemsids_firstcol = $this->getAllItemsIds($itemsFirstColumn);
			$this->_allItemsFirstColumnId = ($itemsFirstColumn)?$itemsids_firstcol:'';
		}
		$this->scopeConfig = $scopeConfig;
	}
	public function getItems()
	{
		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
    	$group_id = $this->scopeConfig->getValue(self::XML_PATH_MEGAMENU_GROUPID, $storeScope);

		$menuGroup = $this->createMenuGroup();
		$group_item = $menuGroup->load($group_id);
		if($group_item->getStatus() == self::STATUS_ENABLED){
			$collection_items = $this->_menuItem->getItemsByLv($group_id, $this->_defaults['start_level']);
			return $collection_items;
		}
		else{
			return array();
		}
	}

}