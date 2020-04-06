<?php

namespace Shopware\Production\Voucher\Service;

use Dompdf\Options;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Content\MailTemplate\Service\MailService;
use Shopware\Core\Framework\Adapter\Twig\StringTemplateRenderer;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Dompdf\Dompdf;
use Shopware\Core\Framework\Validation\DataBag\DataBag;
use Shopware\Core\System\SystemConfig\SystemConfigEntity;
use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;
use Shopware\Production\Portal\Services\TemplateMailSender;
use Twig\Environment;

class VoucherFundingEmailService
{
    public const VOUCHER_PDF_NAME = 'downtown-gutschein.pdf';

    /**
     * @var EntityRepositoryInterface
     */
    private $systemConfigRepository;

    /**
     * @var TemplateMailSender
     */
    private $templateMailSender;

    /**
     * @var Environment
     */
    private $twig;

    public function __construct(
        EntityRepositoryInterface $systemConfigRepository,
        TemplateMailSender $templateMailSender,
        Environment $twig
    ) {
        $this->systemConfigRepository = $systemConfigRepository;
        $this->templateMailSender = $templateMailSender;
        $this->twig = $twig;
    }

    public function sendEmailCustomer(
        array $vouchers,
        MerchantEntity $merchant,
        OrderEntity $order,
        Context $context
    ) : void
    {
        $this->twig->addGlobal('context', $context);
        $this->twig->disableStrictVariables();

        $customerName = sprintf('%s %s %s',
            $order->getOrderCustomer()->getSalutation()->getDisplayName(),
            $order->getOrderCustomer()->getFirstName(),
            $order->getOrderCustomer()->getLastName()
        );

        $templateData = [
            'merchant' => $merchant,
            'order' => $order,
            'customerName' => $customerName,
            'vouchers' => $vouchers,
            'today' => date('d.m.Y')
        ];

        $pdfTemplate = $this->twig->render('@Voucher/pdf-template.html.twig', $templateData);
        $voucherPdf = $this->renderVoucherAttachment($pdfTemplate);

        $modifier = static function (\Swift_Message $message) use($voucherPdf) {
            $message->attach(new \Swift_Attachment($voucherPdf, self::VOUCHER_PDF_NAME, 'application/pdf'));
        };

        $this->templateMailSender->sendMail($merchant->getEmail(), 'voucher_customer', $templateData, $modifier);
    }

    public function sendEmailMerchant(
        array $vouchers,
        MerchantEntity $merchant,
        OrderEntity $order,
        Context $context
    ): void
    {
        $this->twig->addGlobal('context', $context);
        $this->twig->disableStrictVariables();

        $templateData = [
            'merchant' => $merchant,
            'order' => $order,
            'vouchers' => $vouchers,
            'today' => date('d.m.Y')
        ];

        $this->templateMailSender->sendMail($merchant->getEmail(), 'voucher_merchant', $templateData);
    }

    private function renderVoucherAttachment(string $contentTemplate): string
    {
        $options = new Options();
        $options->setDefaultFont('Arial');
        $options->setIsPhpEnabled(true);
        $options->setIsRemoteEnabled(true);

        $dompdf = new Dompdf();
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->setOptions($options);
        $dompdf->loadHtml($contentTemplate);
        $dompdf->render();

        return $dompdf->output();
    }

}
