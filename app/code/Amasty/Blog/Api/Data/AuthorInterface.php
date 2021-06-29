<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


declare(strict_types=1);

namespace Amasty\Blog\Api\Data;

interface AuthorInterface
{
    const ROUTE_AUTHOR = 'author';

    const AUTHOR_ID = 'author_id';

    const NAME = 'name';

    const FACEBOOK_PROFILE = 'facebook_profile';

    const TWITTER_PROFILE = 'twitter_profile';

    const URL_KEY = 'url_key';

    const META_TITLE = 'meta_title';

    const META_TAGS = 'meta_tags';

    const META_DESCRIPTION = 'meta_description';

    const STORE_ID = 'store_id';

    const JOB_TITLE = 'job_title';

    const LINKEDIN_PROFILE = 'linkedin_profile';

    const SHORT_DESCRIPTION = 'short_description';

    const DESCRIPTION = 'description';

    const IMAGE = 'image';

    const FIELDS_BY_STORE = [
        'general' => [
            self::NAME,
            self::JOB_TITLE,
            self::DESCRIPTION,
            self::SHORT_DESCRIPTION,
        ],
        'meta_data' => [
            self::META_TITLE,
            self::META_TAGS,
            self::META_DESCRIPTION
        ]
    ];

    /**
     * @return int
     */
    public function getAuthorId();

    /**
     * @param int $authorId
     *
     * @return \Amasty\Blog\Api\Data\AuthorInterface
     */
    public function setAuthorId($authorId);

    /**
     * @return string|null
     */
    public function getName();

    /**
     * @param string|null $name
     *
     * @return \Amasty\Blog\Api\Data\AuthorInterface
     */
    public function setName($name);

    /**
     * @return string|null
     */
    public function getUrlKey();

    /**
     * @param string|null $urlKey
     *
     * @return \Amasty\Blog\Api\Data\AuthorInterface
     */
    public function setUrlKey($urlKey);

    /**
     * @return string|null
     */
    public function getMetaTitle();

    /**
     * @param string|null $metaTitle
     *
     * @return \Amasty\Blog\Api\Data\AuthorInterface
     */
    public function setMetaTitle($metaTitle);

    /**
     * @return string|null
     */
    public function getMetaTags();

    /**
     * @param string|null $metaTags
     *
     * @return \Amasty\Blog\Api\Data\AuthorInterface
     */
    public function setMetaTags($metaTags);

    /**
     * @return string|null
     */
    public function getMetaDescription();

    /**
     * @param string|null $metaDescription
     *
     * @return \Amasty\Blog\Api\Data\AuthorInterface
     */
    public function setMetaDescription($metaDescription);

    /**
     * @param null $name
     * @return \Amasty\Blog\Api\Data\AuthorInterface
     */
    public function prepapreUrlKey($name = null);

    /**
     * @return string
     */
    public function getFacebookProfile();

    /**
     * @param string $profileLink
     * @return \Amasty\Blog\Api\Data\AuthorInterface
     */
    public function setFacebookProfile($profileLink);

    /**
     * @return string
     */
    public function getTwitterProfile();

    /**
     * @param string $profileLink
     * @return \Amasty\Blog\Api\Data\AuthorInterface
     */
    public function setTwitterProfile($profileLink);

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $storeId
     *
     * @return \Amasty\Blog\Api\Data\AuthorInterface
     */
    public function setStoreId($storeId);

    /**
     * @return string|null
     */
    public function getJobTitle(): ?string;

    /**
     * @param string|null $title
     */
    public function setJobTitle(?string $title): void;

    /**
     * @return string|null
     */
    public function getLinkedinProfile(): ?string;

    /**
     * @param string|null $profileLink
     */
    public function setLinkedinProfile(?string $profileLink): void;

    /**
     * @return string|null
     */
    public function getShortDescription(): ?string;

    /**
     * @param string|null $description
     */
    public function setShortDescription(?string $description): void;

    /**
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void;

    /**
     * @return string|null
     */
    public function getImage(): ?string;

    /**
     * @param string|null $image
     */
    public function setImage(?string $image): void;
}
