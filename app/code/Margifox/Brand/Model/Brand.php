<?php

namespace Margifox\Brand\Model;

use Margifox\Brand\Api\Data\BrandInterface;

class Brand extends \Magento\Framework\Model\AbstractModel implements BrandInterface
{
    protected function _construct()
    {
        $this->_init(ResourceModel\Brand::class);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @return int
     */
    public function getPointsLifetime()
    {
        return $this->getData(self::POINTS_LIFETIME);
    }

    /**
     * @return int|null
     */
    public function getOptionLinkId()
    {
        return $this->getData(self::OPTION_LINK_ID);
    }
}
