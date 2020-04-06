<?php declare(strict_types=1);

namespace Shopware\Production\Voucher\Service;

use Dompdf\Options;
use Shopware\Core\Checkout\Document\FileGenerator\FileGeneratorRegistry;
use Shopware\Core\Checkout\Document\FileGenerator\PdfGenerator;
use Shopware\Core\Checkout\Document\GeneratedDocument;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;
use Dompdf\Dompdf;
use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;
use Shopware\Production\Portal\Services\TemplateMailSender;
use Twig\Environment;

class VoucherFundingEmailService
{
    public const VOUCHER_PDF_NAME = 'voucher.pdf';

    /**
     * @var TemplateMailSender
     */
    private $templateMailSender;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var FileGeneratorRegistry
     */
    private $fileGeneratorRegistry;

    public function __construct(
        TemplateMailSender $templateMailSender,
        Environment $twig,
        FileGeneratorRegistry $fileGeneratorRegistry
    ) {
        $this->templateMailSender = $templateMailSender;
        $this->twig = $twig;
        $this->fileGeneratorRegistry = $fileGeneratorRegistry;
    }

    public function sendEmailCustomer(
        array $templateData,
        MerchantEntity $merchant,
        Context $context
    ) : void
    {
        $this->twig->addGlobal('context', $context);
        $this->twig->disableStrictVariables();

        $pdfTemplate = $this->twig->render('@Voucher/pdf-template.html.twig', $templateData);
        $pdfGenerator = $this->fileGeneratorRegistry->getGenerator(PdfGenerator::FILE_EXTENSION);
        $voucherPdf = $pdfGenerator->generate($this->renderVoucherAttachment($pdfTemplate));

        $modifier = static function (\Swift_Message $message) use($voucherPdf) {
            $message->attach(new \Swift_Attachment($voucherPdf, self::VOUCHER_PDF_NAME, 'application/pdf'));
        };

        $this->templateMailSender->sendMail($merchant->getEmail(), 'voucher_customer', $templateData, $modifier);
    }

    public function sendEmailMerchant(
        array $templateData,
        MerchantEntity $merchant,
        Context $context
    ): void
    {
        $this->twig->addGlobal('context', $context);
        $this->twig->disableStrictVariables();

        $this->templateMailSender->sendMail($merchant->getEmail(), 'voucher_merchant', $templateData);
    }

    private function renderVoucherAttachment(string $htmlContent): GeneratedDocument
    {
        $generatedDocument = new GeneratedDocument();
        $generatedDocument->setHtml($htmlContent);
        $generatedDocument->setFilename(self::VOUCHER_PDF_NAME);
        $generatedDocument->setPageOrientation('landscape');
        $generatedDocument->setPageSize('a4');
        $generatedDocument->setContentType(PdfGenerator::FILE_CONTENT_TYPE);

        return $generatedDocument;
    }

}
