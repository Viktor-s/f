<?php

namespace Furniture\RetailerBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Furniture\FactoryBundle\Entity\Factory;
use Furniture\GoogleServicesBundle\Entity\Interfaces\AddressMarkerInterface;
use Sylius\Component\Translation\Model\AbstractTranslatable;
use Symfony\Component\Validator\Constraints as Assert;

class RetailerProfile extends AbstractTranslatable implements AddressMarkerInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Collection|RetailerUserProfile[]
     */
    private $retailerUserProfiles;

    /**
     * Control demo factories
     *
     * @var Collection|Factory[]
     */
    private $demoFactories;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var array
     */
    private $phones = [];

    /**
     * @var array
     */
    private $emails = [];

    /**
     * @var RetailerProfileLogoImage
     */
    private $logoImage;

    /**
     * @var string
     */
    private $website;

    /**
     * @var string
     */
    private $subtitle;

    /**
     * @var string
     */
    private $description;

    /**
     * @var float
     */
    private $lat;

    /**
     * @var float
     */
    private $lng;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var string
     */
    private $country;

    /**
     * @var string
     */
    private $locality;

    /**
     * @var string
     */
    private $route;

    /**
     * @var string
     */
    private $streetNumber;

    /**
     * @var string
     */
    private $sublocalityLevel1;

    /**
     * @var string
     */
    private $sublocalityLevel2;

    /**
     * @var string
     */
    private $sublocalityLevel3;

    /**
     * @var string
     */
    private $sublocalityLevel4;

    /**
     * @var string
     */
    private $sublocalityLevel5;

    /**
     * @var string
     */
    private $administrativeAreaLevel1;

    /**
     * @var string
     */
    private $administrativeAreaLevel2;

    /**
     * @var string
     */
    private $administrativeAreaLevel3;

    /**
     * @var string
     */
    private $administrativeAreaLevel4;

    /**
     * @var string
     */
    private $administrativeAreaLevel5;

    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->retailerUserProfiles = new ArrayCollection();
        $this->demoFactories = new ArrayCollection();

        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

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
     * Get users
     *
     * @return Collection|RetailerUserProfile[]
     */
    public function getRetailerUserProfiles()
    {
        return $this->retailerUserProfiles;
    }

    /**
     * Set retailer user profiles
     *
     * @param Collection $retailerUserProfiles
     *
     * @return RetailerUserProfile
     */
    public function setRetailerUserProfiles(Collection $retailerUserProfiles)
    {
        $this->retailerUserProfiles = $retailerUserProfiles;

        return $this;
    }

    /**
     * Has retailerUserProfiles?
     *
     * @return bool
     */
    public function hasRetailerUserProfiles()
    {
        return (bool)!$this->retailerUserProfiles->isEmpty();
    }

    /**
     * Has RetailerProfile?
     *
     * @param \Furniture\RetailerBundle\Entity\RetailerUserProfile $retailerUserProfiles
     *
     * @return bool
     */
    public function hasRetailerUserProfile(RetailerUserProfile $retailerUserProfiles)
    {
        return $this->retailerUserProfiles->contains($retailerUserProfiles);
    }


    /**
     * Add retailer user profile
     *
     * @param RetailerUserProfile $retailerUserProfile
     *
     * @return RetailerUserProfile
     */
    public function addRetailerUserProfile(RetailerUserProfile $retailerUserProfile)
    {
        if (!$this->hasRetailerUserProfile($retailerUserProfile)) {
            $retailerUserProfile->setRetailerProfile($this);
            $this->retailerUserProfiles->add($retailerUserProfile);
        }

        return $this;
    }

    /**
     * Remove user profile
     *
     * @param RetailerUserProfile $retailerUserProfile
     *
     * @return RetailerProfile
     */
    public function removeRetailerUserProfile(RetailerUserProfile $retailerUserProfile)
    {
        if ($this->hasRetailerUserProfile($retailerUserProfile)) {
            $this->retailerUserProfiles->removeElement($retailerUserProfile);
        }

        return $this;
    }

    /**
     * Get demo factories
     *
     * @return Collection|Factory[]
     */
    public function getDemoFactories()
    {
        return $this->demoFactories;
    }

    /**
     * Has demo factories?
     *
     * @return bool
     */
    public function hasDemoFactories()
    {
        return count($this->demoFactories) > 0;
    }

    /**
     * Has demo factory?
     *
     * @param Factory $factory
     *
     * @return bool
     */
    public function hasDemoFactory(Factory $factory)
    {
        return $this->demoFactories->exists(function ($index, Factory $item) use ($factory) {
            return $item->getId() == $factory->getId();
        });
    }

    /**
     * Add demo factory
     *
     * @param Factory $factory
     *
     * @return RetailerProfile
     */
    public function addDemoFactory(Factory $factory)
    {
        if (!$this->hasDemoFactory($factory)) {
            $this->demoFactories->add($factory);
        }

        return $this;
    }

    /**
     * Remove demo factory
     *
     * @param Factory $factory
     *
     * @return RetailerProfile
     */
    public function removeDemoFactory(Factory $factory)
    {
        if ($this->hasDemoFactory($factory)) {
            $key = null;

            $this->demoFactories->forAll(function ($index, Factory $item) use (&$key, $factory) {
                if ($item->getId() == $factory->getId()) {
                    $key = $index;

                    return false;
                }

                return true;
            });

            if ($key) {
                $this->demoFactories->remove($key);
            }
        }

        return $this;
    }

    /**
     * Is demo?
     *
     * @return bool
     */
    public function isDemo()
    {
        return $this->hasDemoFactories();
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return RetailerProfile
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        /** @var RetailerProfileTranslation $translation */
        $translation = $this->translate();

        return $translation->getAddress();
    }

    /**
     * Set phones
     *
     * @param array $phones
     *
     * @return RetailerProfile
     */
    public function setPhones(array $phones)
    {
        $this->phones = $phones;

        return $this;
    }

    /**
     * Get phones
     *
     * @return string
     */
    public function getPhones()
    {
        return $this->phones;
    }

    /**
     * Set emails
     *
     * @param array $emails
     *
     * @return RetailerProfile
     */
    public function setEmails(array $emails)
    {
        $this->emails = $emails;

        return $this;
    }

    /**
     * Get emails
     *
     * @return array
     */
    public function getEmails()
    {
        return $this->emails;
    }

    /**
     * Get logo image
     *
     * @return RetailerProfileLogoImage
     */
    public function getLogoImage()
    {
        return $this->logoImage;
    }

    /**
     * Remove logo image
     *
     * @return RetailerProfileLogoImage
     */
    public function removeLogoImage()
    {
        $logoImage = $this->logoImage;
        $this->logoImage = null;

        return $logoImage;
    }

    /**
     * Set logo image
     *
     * @param RetailerProfileLogoImage $logoImage
     *
     * @return RetailerProfile
     */
    public function setLogoImage(RetailerProfileLogoImage $logoImage)
    {
        $logoImage->setRetailerProfile($this);
        $this->logoImage = $logoImage;

        return $this;
    }

    /**
     * Get website
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set website
     *
     * @param string $website
     *
     * @return RetailerProfile
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get sub title
     *
     * @return string
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * Set sub title
     *
     * @param string $subtitle
     *
     * @return RetailerProfile
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return RetailerProfile
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     *
     * @return float
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     *
     * @return float
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     *
     * @param float $latitude
     *
     * @return \Furniture\RetailerBundle\Entity\RetailerProfile
     */
    public function setLat($latitude)
    {
        $this->lat = $latitude;

        return $this;
    }

    /**
     *
     * @param float $longtitude
     *
     * @return \Furniture\RetailerBundle\Entity\RetailerProfile
     */
    public function setLng($longtitude)
    {
        $this->lng = $longtitude;

        return $this;
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
     * Get updated at
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * On update
     */
    public function onUpdate()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     *
     * @return string
     */
    public function getAdministrativeAreaLevel1()
    {
        return $this->administrativeAreaLevel1;
    }

    /**
     *
     * @return string
     */
    public function getAdministrativeAreaLevel2()
    {
        return $this->administrativeAreaLevel2;
    }

    /**
     *
     * @return string
     */
    public function getAdministrativeAreaLevel3()
    {
        return $this->administrativeAreaLevel3;
    }

    /**
     *
     * @return string
     */
    public function getAdministrativeAreaLevel4()
    {
        return $this->administrativeAreaLevel4;
    }

    /**
     *
     * @return string
     */
    public function getAdministrativeAreaLevel5()
    {
        return $this->administrativeAreaLevel5;
    }

    /**
     * Get county
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     *
     * @return string
     */
    public function getLocality()
    {
        return $this->localy;
    }

    /**
     *
     * @param string $levelArea
     *
     * @return \Furniture\RetailerBundle\Entity\RetailerProfile
     */
    public function setAdministrativeAreaLevel1($levelArea)
    {
        $this->administrativeAreaLevel1 = $levelArea;

        return $this;
    }

    /**
     *
     * @param string $levelArea
     *
     * @return \Furniture\RetailerBundle\Entity\RetailerProfile
     */
    public function setAdministrativeAreaLevel2($levelArea)
    {
        $this->administrativeAreaLevel2 = $levelArea;

        return $this;
    }

    /**
     *
     * @param string $levelArea
     *
     * @return \Furniture\RetailerBundle\Entity\RetailerProfile
     */
    public function setAdministrativeAreaLevel3($levelArea)
    {
        $this->administrativeAreaLevel3 = $levelArea;

        return $this;
    }

    /**
     *
     * @param string $levelArea
     *
     * @return \Furniture\RetailerBundle\Entity\RetailerProfile
     */
    public function setAdministrativeAreaLevel4($levelArea)
    {
        $this->administrativeAreaLevel4 = $levelArea;

        return $this;
    }

    /**
     *
     * @param string $levelArea
     *
     * @return \Furniture\RetailerBundle\Entity\RetailerProfile
     */
    public function setAdministrativeAreaLevel5($levelArea)
    {
        $this->administrativeAreaLevel5 = $levelArea;

        return $this;
    }

    /**
     *
     * @param string $country
     *
     * @return RetailerProfile
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     *
     * @param string $localy
     *
     * @return RetailerProfile
     */
    public function setLocality($localy)
    {
        $this->locality = $localy;

        return $this;
    }

    /**
     * @return string
     */
    public function getStreetNumber()
    {
        return $this->streetNumber;
    }

    /**
     *
     * @param string $streetNumber
     *
     * @return RetailerProfile
     */
    public function setStreetNumber($streetNumber)
    {
        $this->streetNumber = $streetNumber;

        return $this;
    }

    /**
     *
     * @return string
     */
    public function getSublocalityLevel1()
    {
        return $this->sublocalityLevel1;
    }

    /**
     *
     * @return string
     */
    public function getSublocalityLevel2()
    {
        return $this->sublocalityLevel2;
    }

    /**
     *
     * @return string
     */
    public function getSublocalityLevel3()
    {
        return $this->sublocalityLevel3;
    }

    /**
     *
     * @return string
     */
    public function getSublocalityLevel4()
    {
        return $this->sublocalityLevel4;
    }

    /**
     *
     * @return string
     */
    public function getSublocalityLevel5()
    {
        return $this->sublocalityLevel5;
    }

    /**
     *
     * @param string $levelArea
     *
     * @return RetailerProfile
     */
    public function setSublocalityLevel1($levelArea)
    {
        $this->sublocalityLevel1 = $levelArea;

        return $this;
    }

    /**
     *
     * @param string $levelArea
     *
     * @return RetailerProfile
     */
    public function setSublocalityLevel2($levelArea)
    {
        $this->sublocalityLevel2 = $levelArea;

        return $this;
    }

    /**
     *
     * @param string $levelArea
     *
     * @return RetailerProfile
     */
    public function setSublocalityLevel3($levelArea)
    {
        $this->sublocalityLevel3 = $levelArea;

        return $this;
    }

    /**
     *
     * @param string $levelArea
     *
     * @return RetailerProfile
     */
    public function setSublocalityLevel4($levelArea)
    {
        $this->sublocalityLevel4 = $levelArea;

        return $this;
    }

    /**
     * @param string $levelArea
     *
     * @return RetailerProfile
     */
    public function setSublocalityLevel5($levelArea)
    {
        $this->sublocalityLevel5 = $levelArea;

        return $this;
    }

    /**
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set route
     *
     * @param string $route
     *
     * @return RetailerProfile
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Implement __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ?: '';
    }
}
