<?php
namespace Fooman\PdfCustomiser\Model\PdfRenderer;

use Fooman\EmailAttachments\Model\Api\PdfRendererInterface;

/**
 * @author     Kristof Ringleff
 * @package    Fooman_PdfCustomiser
 * @copyright  Copyright (c) 2009 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class CreditmemoAdapter implements PdfRendererInterface
{

    private $pdfRendererFactory;

    private $pdfRenderer;

    private $creditmemoDocumentFactory;

    public function __construct(
        \Fooman\PdfCore\Model\PdfRendererFactory $pdfRendererFactory,
        \Fooman\PdfCustomiser\Block\CreditmemoFactory $creditmemoDocumentFactory
    ) {
        $this->pdfRendererFactory = $pdfRendererFactory;
        $this->creditmemoDocumentFactory = $creditmemoDocumentFactory;
    }

    public function getPdfAsString(array $salesObjects)
    {
        $this->pdfRenderer = $this->pdfRendererFactory->create();
        foreach ($salesObjects as $creditmemo) {
            $document = $this->creditmemoDocumentFactory->create(
                ['data' => ['creditmemo' => $creditmemo]]
            );

            $this->pdfRenderer->addDocument($document);
        }

        return $this->pdfRenderer->getPdfAsString();
    }

    public function getFileName()
    {
        return $this->pdfRenderer->getFilename(true);
    }

    public function canRender()
    {
        return true;
    }
}
