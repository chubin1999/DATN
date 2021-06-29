<?php

namespace Margifox\Brand\Api\Data;

interface BrandInterface
{
    const ID = 'entity_id';
    const NAME = 'name';
    const POINTS_LIFETIME = 'points_lifetime';
    const OPTION_LINK_ID = 'attribute_option_link_id';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return int
     */
    public function getPointsLifetime();

    /**
     * @return int|null
     */
    public function getOptionLinkId();
}
