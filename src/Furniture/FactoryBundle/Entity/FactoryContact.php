<?php

namespace Furniture\FactoryBundle\Entity;

use Sylius\Component\Translation\Model\AbstractTranslatable;
use Symfony\Component\Validator\Constraints as Assert;

class FactoryContact extends AbstractTranslatable
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var \Doctrine\Common\Collections\Collection|FactoryContactTranslation[]
     *
     * @Assert\Valid()
     */
    protected $translations;

    /**
     * The department name (Example: Selva s.p.a., W.W.T.S. Italia srl)
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private $departmentName;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private $address;

    /**
     * @var array
     */
    private $emails = [];

    /**
     * @var array
     */
    private $phones = [];

    /**
     * @var array
     */
    private $sites = [];

    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();
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
     * Get factory
     *
     * @return Factory
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Get factory
     *
     * @param Factory $factory
     *
     * @return FactoryContact
     */
    public function setFactory($factory)
    {
        $this->factory = $factory;

        return $this;
    }

    /**
     * Get department name
     *
     * @return string
     */
    public function getDepartmentName()
    {
        return $this->departmentName;
    }

    /**
     * Set department name
     *
     * @param string $departmentName
     *
     * @return FactoryContact
     */
    public function setDepartmentName($departmentName)
    {
        $this->departmentName = $departmentName;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return FactoryContact
     */
    public function setAddress($address)
    {
        $this->address = $address;

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
     * Set emails
     *
     * @param array $emails
     *
     * @return FactoryContact
     */
    public function setEmails(array $emails)
    {
        $this->emails = $emails;

        return $this;
    }

    /**
     * Get phones
     *
     * @return array
     */
    public function getPhones()
    {
        return $this->phones;
    }

    /**
     * Set phones
     *
     * @param array $phones
     *
     * @return FactoryContact
     */
    public function setPhones($phones)
    {
        $this->phones = $phones;

        return $this;
    }

    /**
     * Get sites
     *
     * @return array
     */
    public function getSites()
    {
        return $this->sites;
    }

    /**
     * Set sites
     *
     * @param array $sites
     *
     * @return FactoryContact
     */
    public function setSites(array $sites)
    {
        $this->sites = $sites;
    }
}
