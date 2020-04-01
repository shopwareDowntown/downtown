<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Storefront\Pagelet\Footer;

use Shopware\Core\Framework\Struct\Struct;

class PortalStruct extends Struct
{
    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * @var string
     */
    protected $url;

    public function getUrl(): string
    {
        return $this->url;
    }
}
