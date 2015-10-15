<?php

namespace Furniture\FactoryBundle\Entity;

use Furniture\CommonBundle\Entity\User;
use Furniture\ProductBundle\Entity\Product;
use Sylius\Component\Translation\Model\AbstractTranslatable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @var Collection|FactoryUserRelation[]
     */
    protected $userRelations;

    /**
     * @var Collection|\Furniture\CommonBundle\Entity\User[]
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
     * Construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->createdAt = new \DateTime();
        $this->images = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->userRelations = new ArrayCollection();
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
     * Has user relations
     *
     * @return bool
     */
    public function hasUserRelations()
    {
        return (bool)!$this->userRelations->isEmpty();
    }
    
    /**
     * Get user relations
     *
     * @return Collection
     */
    public function getUserRelations()
    {
        return $this->userRelations;
    }

    /**
     * Get users relation by user
     *
     * @param User $user
     *
     * @return FactoryUserRelation|null
     */
    public function getUserRelationByUser(User $user)
    {
        foreach ($this->userRelations as $userRelation) {
            if ($userRelation->getUser()->getId() == $user->getId()) {
                return $userRelation;
            }
        }

        return null;
    }
    
    /**
     * Set user relations
     *
     * @param Collection $userRelations
     *
     * @return Factory
     */
    public function setUserRelations(Collection $userRelations)
    {
        $this->userRelations = $userRelations;

        return $this;
    }
    
    /**
     * Has user relation?
     *
     * @param FactoryUserRelation $userRelation
     *
     * @return bool
     */
    public function hasUserRelation(FactoryUserRelation $userRelation)
    {
        return $this->userRelations->contains($userRelation);
    }
    
    /**
     * Add user relation
     *
     * @param FactoryUserRelation $userRelation
     *
     * @return Factory
     */
    public function addUserRelation(FactoryUserRelation $userRelation)
    {
        if(!$this->hasUserRelation($userRelation)){
            $userRelation->setFactory($this);
            $this->userRelations->add($userRelation);
        }

        return $this;
    }
    
    /**
     * Remove user relation
     *
     * @param FactoryUserRelation $userRelation
     *
     * @return Factory
     */
    public function removeUserRelation(FactoryUserRelation $userRelation)
    {
        if($this->hasUserRelation($userRelation)){
            $this->userRelations->removeElement($userRelation);
        }

        return $this;
    }

    /**
     * Get users
     *
     * @return Collection|\Furniture\CommonBundle\Entity\User
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
        return $this->contacts;
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
     * Implement __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name ?: '';
    }
}
