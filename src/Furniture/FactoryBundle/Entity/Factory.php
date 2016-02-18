<?php

namespace Furniture\FactoryBundle\Entity;

use Furniture\ProductBundle\Entity\Product;
use Furniture\RetailerBundle\Entity\RetailerProfile;
use Sylius\Component\Translation\Model\AbstractTranslatable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Criteria;

class Factory extends AbstractTranslatable
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     */
    protected $name;
    
    /**
     * @var \DateTime
     */
    protected $createdAt;
    
    /**
     * @var Collection|FactoryImage[]
     */
    protected $images;
    
    /**
     * @var FactoryLogoImage
     */
    protected $logoImage;
    
    /**
     * @var Collection
     */
    protected $products;
    
    /**
     * @var Collection|FactoryRetailerRelation[]
     */
    protected $retailerRelations;

    /**
     * @var Collection|\Furniture\UserBundle\Entity\User[]
     */
    protected $users;

    /**
     * @var FactoryDefaultRelation
     */
    protected $defaultRelation;

    /**
     * @var Collection|FactoryContact[]
     *
     * @Assert\Valid()
     */
    private $contacts;

    /**
     * @var bool
     */
    private $enabled;
    
    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->createdAt = new \DateTime();
        $this->images = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->retailerRelations = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->defaultRelation = new FactoryDefaultRelation($this);
        $this->contacts = new ArrayCollection();
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
     * Get created at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    
    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Set name
     *
     * @param string $name
     *
     * @return Factory
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
    
    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->translate()->getDescription();
    }
    
    /**
     * Set description
     *
     * @param string $description
     *
     * @return Factory
     */
    public function setDescription($description)
    {
        $this->translate()->setDescription($description);

        return $this;
    }
    
    /**
     * Get short description
     *
     * @return string
     */
    public function getShortDescription()
    {
        return $this->translate()->getShortDescription();
    }
    
    /**
     * Set short description
     *
     * @param string $description
     *
     * @return Factory
     */
    public function setShortDescription($description)
    {
        $this->translate()->setShortDescription($description);

        return $this;
    }
    
    /**
     * Is has images?
     *
     * @return bool
     */
    public function hasImages()
    {
        return (bool)!$this->images->isEmpty();
    }
    
    /**
     * Get images
     *
     * @return Collection
     */
    public function getImages()
    {
        return $this->images;
    }
    
    /**
     * Set images
     *
     * @param Collection $images
     *
     * @return Factory
     */
    public function setImages(Collection $images)
    {
        $this->images = $images;

        return $this;
    }
    
    /**
     * Has image
     *
     * @param FactoryImage $image
     *
     * @return bool
     */
    public function hasImage(FactoryImage $image)
    {
        return $this->images->contains($image);
    }
    
    /**
     * Add image
     *
     * @param FactoryImage $image
     *
     * @return Factory
     */
    public function addImage(FactoryImage $image)
    {
        if(!$this->hasImage($image)){
            $image->setFactory($this);
            $this->images->add($image);
        }

        return $this;
    }
    
    /**
     * Remove image
     *
     * @param FactoryImage $image
     *
     * @return Factory
     */
    public function removeImage(FactoryImage $image)
    {
        if($this->hasImage($image)){
            $this->images->removeElement($image);
        }

        return $this;
    }

    /**
     * Get primary image
     * Now returns first image
     *
     * @return FactoryImage
     */
    public function getPrimaryImage()
    {
        return $this->images->first() ?: null;
    }
    
    /**
     * 
     * @return \Furniture\FactoryBundle\Entity\FactoryLogoImage
     */
    public function getLogoImage()
    {
        return $this->logoImage;
    }
    
    /**
     * 
     * @param \Furniture\FactoryBundle\Entity\FactoryLogoImage $logo
     * @return \Furniture\FactoryBundle\Entity\Factory
     */
    public function setLogoImage(FactoryLogoImage $logo)
    {
        $logo->setFactory($this);
        $this->logoImage = $logo;
        return $this;
    }


    /**
     * Has products
     *
     * @return bool
     */
    public function hasProducts()
    {
        return (bool)!$this->products->isEmpty();
    }
    
    /**
     * Get products
     *
     * @return Collection
     */
    public function getProducts()
    {
        return $this->products;
    }
    
    /**
     * Set products
     *
     * @param Collection $products
     *
     * @return Factory
     */
    public function setProducts(Collection $products)
    {
        $this->products = $products;

        return $this;
    }
    
    /**
     * Has product
     *
     * @param Product $product
     *
     * @return bool
     */
    public function hasProduct(Product $product)
    {
        return $this->products->contains($product);
    }
    
    /**
     * Add product
     *
     * @param Product $product
     *
     * @return Factory
     */
    public function addProduct(Product $product)
    {
        if(!$this->hasProduct($product)){
            $this->products->add($product);
        }

        return $this;
    }
    
    /**
     * Remove product
     *
     * @param Product $product
     *
     * @return Factory
     */
    public function removeProduct(Product $product)
    {
        if($this->hasProduct($product)){
            $this->products->removeElement($product);
        }

        return $this;
    }
    
    /**
     * Has retailer relations
     *
     * @return bool
     */
    public function hasRetailerRelations()
    {
        return (bool)!$this->retailerRelations->isEmpty();
    }
    
    /**
     * Get retailer relations
     *
     * @return Collection
     */
    public function getRetailerRelations()
    {
        return $this->retailerRelations;
    }

    /**
     * Get retailer relation by user
     *
     * @param RetailerProfile $retailer
     *
     * @return FactoryRetailerRelation|null
     */
    public function getRetailerRelationByRetailer(RetailerProfile $retailer)
    {
        foreach ($this->retailerRelations as $relation) {
            if ($relation->getRetailer()->getId() == $retailer->getId()) {
                return $relation;
            }
        }

        return null;
    }
    
    /**
     * Set retailer relations
     *
     * @param Collection $retailerRelations
     *
     * @return Factory
     */
    public function setRetailerRelations(Collection $retailerRelations)
    {
        $this->retailerRelations = $retailerRelations;

        return $this;
    }
    
    /**
     * Has retailer relation?
     *
     * @param FactoryRetailerRelation $userRelation
     *
     * @return bool
     */
    public function hasRetailerRelation(FactoryRetailerRelation $userRelation)
    {
        return $this->retailerRelations->contains($userRelation);
    }
    
    /**
     * Add retailer relation
     *
     * @param FactoryRetailerRelation $userRelation
     *
     * @return Factory
     */
    public function addRetailerRelation(FactoryRetailerRelation $userRelation)
    {
        if(!$this->hasRetailerRelation($userRelation)){
            $userRelation->setFactory($this);
            $this->retailerRelations->add($userRelation);
        }

        return $this;
    }
    
    /**
     * Remove retailer relation
     *
     * @param FactoryRetailerRelation $userRelation
     *
     * @return Factory
     */
    public function removeRetailerRelation(FactoryRetailerRelation $userRelation)
    {
        if($this->hasRetailerRelation($userRelation)){
            $this->retailerRelations->removeElement($userRelation);
        }

        return $this;
    }

    /**
     * Get users
     *
     * @return Collection|\Furniture\UserBundle\Entity\User
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Get default relation
     *
     * @return FactoryDefaultRelation
     */
    public function getDefaultRelation()
    {
        return $this->defaultRelation;
    }

    /**
     * Set contacts
     *
     * @param Collection|FactoryContact[] $contacts
     *
     * @return Factory
     */
    public function setContacts(Collection $contacts)
    {
        $this->contacts = $contacts;

        return $this;
    }

    /**
     * Get contacts
     *
     * @return Collection|FactoryContact[]
     */
    public function getContacts()
    {
        $order = new Criteria();
        $order->orderBy(Array(
            'position' => Criteria::ASC
        ));
        return $this->contacts->matching($order);;
    }

    /**
     * Has contact?
     *
     * @param FactoryContact $contact
     *
     * @return bool
     */
    public function hasContact(FactoryContact $contact)
    {
        return $this->contacts->contains($contact);
    }

    /**
     * Add contact
     *
     * @param FactoryContact $contact
     *
     * @return Factory
     */
    public function addContact(FactoryContact $contact)
    {
        if (!$this->hasContact($contact)) {
            $contact->setFactory($this);
            $this->contacts->add($contact);
        }

        return $this;
    }

    /**
     * Remove contact
     *
     * @param FactoryContact $contact
     *
     * @return Factory
     */
    public function removeContact(FactoryContact $contact)
    {
        if ($this->hasContact($contact)) {
            $this->contacts->removeElement($contact);
        }

        return $this;
    }

    /**
     * Get enabled status
     *
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }
    
    /**
     * Set enabled status
     *
     * @param bool $enabled
     *
     * @return Factory
     */
    public function setEnabled($enabled)
    {
        $this->enabled = (bool)$enabled;

        return $this;
    }

    /**
     * Is enabled?
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Is disabled?
     *
     * @return bool
     */
    public function isDisabled()
    {
        return !$this->isEnabled();
    }

    /**
     * Implement __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name ?: '';
    }
}
