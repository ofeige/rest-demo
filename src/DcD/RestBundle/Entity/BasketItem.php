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
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isDeleted = false;

    /**
     * @var $createdAt
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var $deletedAt;
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime("now"));
    }

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

    /**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     *
     * @return BasketItem
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;
        $this->setDeletedAt(new \DateTime("now"));

        return $this;
    }

    /**
     * Get isDeleted
     *
     * @return boolean
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return BasketItem
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     *
     * @return BasketItem
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Get deletedAt
     *
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }
}
