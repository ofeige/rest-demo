<?php

namespace DcD\RestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Basket
 *
 * @ORM\Table(name="basket")
 * @ORM\Entity(repositoryClass="DcD\RestBundle\Repository\BasketRepository")
 * @UniqueEntity("uuid")
 */
class Basket
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
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     * @Assert\NotBlank(message="userId is missing")
     * @Assert\Type(type="integer")
     * @Assert\GreaterThan(0)
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=36, unique=true)
     * @Assert\NotBlank(message="uuid is missing")
     */
    private $uuid;

    /**
     * @OneToMany(targetEntity="BasketItem", mappedBy="basket", fetch="EXTRA_LAZY")
     */
    private $basketItems;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return Basket
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     *
     * @return Basket
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->basketItems = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add basketItem
     *
     * @param \DcD\RestBundle\Entity\BasketItem $basketItem
     *
     * @return Basket
     */
    public function addBasketItem(\DcD\RestBundle\Entity\BasketItem $basketItem)
    {
        $this->basketItems[] = $basketItem;

        return $this;
    }

    /**
     * Remove basketItem
     *
     * @param \DcD\RestBundle\Entity\BasketItem $basketItem
     */
    public function removeBasketItem(\DcD\RestBundle\Entity\BasketItem $basketItem)
    {
        $this->basketItems->removeElement($basketItem);
    }

    /**
     * Get basketItems
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBasketItems()
    {
        return $this->basketItems;
    }
}
