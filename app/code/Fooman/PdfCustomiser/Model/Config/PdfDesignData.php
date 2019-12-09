<?php
namespace Fooman\PdfCustomiser\Model\Config;

/**
 * @author     Kristof Ringleff
 * @package    Fooman_PdfCustomiser
 * @copyright  Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class PdfDesignData extends \Magento\Framework\Config\Data
{
    protected $preppedData = [];

    protected function getPreppedData()
    {
        if (empty($this->preppedData)) {
            foreach ($this->get('config') as $key => $value) {
                if (isset($value['pdfDesign'])) {
                    foreach ($value['pdfDesign'] as $design) {
                        $id = $design['__attributes__']['id'];
                        $this->preppedData[$id] = [
                            'id' => $id,
                            'classname' => $design['__attributes__']['classname'],
                            'name' => $design['__attributes__']['name']
                        ];
                    }
                }
            }
        }
        return $this->preppedData;
    }

    public function getClassForDesign($id)
    {
        $data = $this->getPreppedData();
        if (!isset($data[$id])) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Requested design does not exist')
            );
        }
        return $data[$id]['classname'];
    }

    public function getPdfDesignOptions()
    {
        $options = [];
        foreach ($this->getPreppedData() as $design) {
            $options[] = ['value' => $design['id'], 'label' => $design['name']];
        }
        return $options;
    }
}
