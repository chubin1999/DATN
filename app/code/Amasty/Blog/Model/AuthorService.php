<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


declare(strict_types=1);

namespace Amasty\Blog\Model;

use Amasty\Blog\Api\AuthorRepositoryInterface;
use Amasty\Blog\Api\Data\AuthorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class AuthorService
{
    /**
     * @var AuthorRepositoryInterface
     */
    private $authorRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var AuthorInterface
     */
    private $currentAuthor;

    public function __construct(
        AuthorRepositoryInterface $authorRepository,
        StoreManagerInterface $storeManager,
        Registry $registry,
        LoggerInterface $logger
    ) {
        $this->authorRepository = $authorRepository;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->registry = $registry;
    }

    public function getCurrentAuthor(?int $authorId = null): ?AuthorInterface
    {
        if ($this->currentAuthor === null) {
            if ($authorId) {
                $this->currentAuthor = $this->getAuthor($authorId);
            } else {
                $currentPost = $this->registry->registry('current_post');
                if ($currentPost && $currentPost->getAuthorId()) {
                    $this->currentAuthor = $this->getAuthor((int)$currentPost->getAuthorId());
                }
            }
        }

        return $this->currentAuthor;
    }

    private function getAuthor(int $authorId): ?AuthorInterface
    {
        try {
            /** @var AuthorInterface $author */
            $author = $this->authorRepository->getByIdAndStore($authorId, $this->storeManager->getStore()->getId());
        } catch (\Exception $e) {
            $author = null;
        }

        return $author;
    }
}
