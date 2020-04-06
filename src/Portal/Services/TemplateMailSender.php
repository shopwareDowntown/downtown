<?php declare(strict_types=1);

namespace Shopware\Production\Portal\Services;

use Shopware\Core\Content\MailTemplate\Service\MailSender;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class TemplateMailSender
{
    private const PATH = 'email/%s.html.twig';

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var MailSender
     */
    private $mailService;

    /**
     * @var SystemConfigService
     */
    private $systemConfigService;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        Environment $twig,
        MailSender $mailService,
        SystemConfigService $systemConfigService,
        TranslatorInterface $translator
    ) {
        $this->twig = $twig;
        $this->mailService = $mailService;
        $this->systemConfigService = $systemConfigService;
        $this->translator = $translator;
    }

    public function sendMail(string $receiver, string $template, array $variables, ?callable $mailModifier = null): void
    {
        $templateFile = sprintf(self::PATH, $template);

        $translatedTemplateFile = sprintf(self::PATH, $template . '-' . $this->translator->getLocale());

        if ($this->twig->getLoader()->exists($translatedTemplateFile)) {
            $templateFile = $translatedTemplateFile;
        }

        $subject = $this->translator->trans('mail.' . $template . '.subject');

        $senderEmail = $this->systemConfigService->get('core.basicInformation.email');

        $mail = new \Swift_Message($subject);
        $mail->addTo($receiver);
        $mail->addFrom($senderEmail);
        $mail->setBody($this->twig->render($templateFile, $variables), 'text/html');

        if ($mailModifier) {
            $mailModifier($mail);
        }

        $this->mailService->send($mail);
    }
}
