<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model;

use JsonSerializable;
use PayNL\Sdk\Packages\Doctrine\Common\Collections\ArrayCollection;
use PayNL\Sdk\Common\{
    JsonSerializeTrait,
    CollectionInterface
};

/**
 * Class Products
 *
 * @package PayNL\Sdk\Model
 */
class Products extends ArrayCollection implements ModelInterface, CollectionInterface, JsonSerializable
{
    use JsonSerializeTrait;

    /**
     * @return array
     */
    public function getProducts(): array
    {
        return $this->toArray();
    }

    /**
     * @param array $products
     *
     * @return Products
     */
    public function setProducts(array $products): self
    {
        $this->clear();

        if (0 === count($products)) {
            return $this;
        }

        foreach ($products as $product) {
            $this->addProduct($product);
        }

        return $this;
    }

    /**
     * @param Product $product
     *
     * @return Products
     */
    public function addProduct(Product $product): self
    {
        $this->set($product->getId(), $product);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCollectionName(): string
    {
        return 'products';
    }
}
