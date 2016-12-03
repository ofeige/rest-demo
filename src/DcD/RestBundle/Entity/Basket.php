<?php

namespace DcD\RestBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Basket
 *
 * @ORM\Table(name="basket", indexes={@ORM\Index(name="is_deleted_idx", columns={"is_deleted"})})
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
     * @ORM\Column(type="integer")
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
        $this->basketItems = new ArrayCollection();
        $this->setCreatedAt(new \DateTime("now"));
    }

    /**
     * Add basketItem
     *
     * @param BasketItem $basketItem
     *
     * @return Basket
     */
    public function addBasketItem(BasketItem $basketItem)
    {
        $this->basketItems[] = $basketItem;

        return $this;
    }

    /**
     * Remove basketItem
     *
     * @param BasketItem $basketItem
     */
    public function removeBasketItem(BasketItem $basketItem)
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

    /**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     *
     * @return Basket
     */
    public function setDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;
        $this->setDeletedAt(new \DateTime("now"));

        foreach ($this->basketItems as $basketItem) {
            $basketItem->setDeleted(true);
        }

        return $this;
    }

    /**
     * Get isDeleted
     *
     * @return boolean
     */
    public function isDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Basket
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
     * @return Basket
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
