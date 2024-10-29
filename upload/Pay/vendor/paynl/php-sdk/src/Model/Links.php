<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model;

use PayNL\Sdk\Packages\Doctrine\Common\Collections\ArrayCollection;
use PayNL\Sdk\Common\CollectionInterface;

/**
 * Class Links
 *
 * @package PayNL\Sdk\Model
 */
class Links extends ArrayCollection implements ModelInterface, CollectionInterface
{
    /**
     * @return array
     */
    public function getLinks(): array
    {
        return $this->toArray();
    }

    /**
     * @param array $links
     *
     * @return Links
     */
    public function setLinks(array $links): self
    {
        // reset
        $this->clear();

        if (0 === count($links)) {
            return $this;
        }

        foreach ($links as $link) {
            $this->addLink($link);
        }
        return $this;
    }

    /**
     * @param Link $link
     *
     * @return Links
     */
    public function addLink(Link $link): self
    {
        $this->set($link->getRel(), $link);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCollectionName(): string
    {
        return 'links';
    }
}
