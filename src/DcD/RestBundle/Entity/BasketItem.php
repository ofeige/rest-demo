<?php

namespace DcD\RestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * BasketItem
 *
 * @ORM\Table(name="basket_item")
 * @ORM\Entity(repositoryClass="DcD\RestBundle\Repository\BasketItemRepository")
 */
class BasketItem
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="Basket", inversedBy="basketItems", fetch="EXTRA_LAZY")
     * @JoinColumn(name="basket_id", referencedColumnName="id", onDelete="cascade")
     */
    private $basket;

    /**
     * @var array
     *
     * @ORM\Column(name="info", type="json_array")
     */
    private $info;



    /**
     * Set info
     *
     * @param array $info
     *
     * @return BasketItem
     */
    public function setInfo($info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Get info
     *
     * @return array
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Set basket
     *
     * @param \DcD\RestBundle\Entity\Basket $basket
     *
     * @return BasketItem
     */
    public function setBasket(\DcD\RestBundle\Entity\Basket $basket = null)
    {
        $this->basket = $basket;

        return $this;
    }

    /**
     * Get basket
     *
     * @return \DcD\RestBundle\Entity\Basket
     */
    public function getBasket()
    {
        return $this->basket;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
