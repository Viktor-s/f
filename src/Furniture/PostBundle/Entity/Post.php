<?php

namespace Furniture\PostBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Furniture\UserBundle\Entity\User;
use Furniture\FactoryBundle\Entity\Factory;
use Sylius\Component\Translation\Model\AbstractTranslatable;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @UniqueEntity("slug")
 */
class Post extends AbstractTranslatable
{
    const TYPE_NEWS     = 1;
    const TYPE_CIRCULAR = 2;

    /**
     * @var int
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection|PostTranslation[]
     *
     * @Assert\Valid
     */
    protected $translations;

    /**
     * The name for display on administer pages
     *
     * @var string
     *
     * @Assert\NotBlank()
     */
    private $displayName;

    /**
     * @var string
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @var int
     */
    private $type = self::TYPE_NEWS;

    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var User
     */
    private $creator;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var \DateTime
     */
    private $publishedAt;

    /**
     * @var Collection|PostImage[]
     */
    private $images;

    /**
     * @var Collection|PostFile[]
     */
    private $files;

    /**
     * @var boolean
     */
    private $useOnSlider = false;

    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->images = new ArrayCollection();
        $this->files = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->publishedAt = new \DateTime();
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
     * Set display name
     *
     * @param string $name
     *
     * @return Post
     */
    public function setDisplayName($name)
    {
        $this->displayName = $name;

        return $this;
    }

    /**
     * Get display name
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Post
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set type
     *
     * @param int $type
     *
     * @return Post
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set creator
     *
     * @param User $creator
     *
     * @return Post
     */
    public function setCreator(User $creator)
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * Get creator
     *
     * @return User
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * Set factory
     *
     * @param Factory $factory
     *
     * @return Post
     */
    public function setFactory(Factory $factory = null)
    {
        $this->factory = $factory;

        return $this;
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
     * Set published at
     *
     * @param \DateTime $publishedAt
     *
     * @return Post
     */
    public function setPublishedAt(\DateTime $publishedAt)
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * Get published at
     *
     * @return \DateTime
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * Set images
     *
     * @param Collection|PostImage[] $images
     *
     * @return Post
     */
    public function setImages(Collection $images)
    {
        $this->images = $images;

        return $this;
    }

    /**
     * Get images
     *
     * @return Collection|PostImage[]
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Has image
     *
     * @param PostImage $image
     *
     * @return bool
     */
    public function hasImage(PostImage $image)
    {
        return $this->images->contains($image);
    }

    /**
     * Add image
     *
     * @param PostImage $image
     *
     * @return Factory
     */
    public function addImage(PostImage $image)
    {
        if(!$this->hasImage($image)){
            $image->setPost($this);
            $this->images->add($image);
        }

        return $this;
    }

    /**
     * Remove image
     *
     * @param PostImage $image
     *
     * @return Factory
     */
    public function removeImage(PostImage $image)
    {
        if($this->hasImage($image)){
            $this->images->removeElement($image);
        }

        return $this;
    }

    /**
     * Set files
     *
     * @param Collection|PostFile[] $files
     *
     * @return Post
     */
    public function setFiles(Collection $files)
    {
        $this->files = $files;

        return $this;
    }

    /**
     * Get files
     *
     * @return Collection|PostFile[]
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Has file?
     *
     * @param PostFile $file
     *
     * @return bool
     */
    public function hasFile(PostFile $file)
    {
        return $this->files->contains($file);
    }

    /**
     * Add file
     *
     * @param PostFile $file
     *
     * @return Post
     */
    public function addFile(PostFile $file)
    {
        if (!$this->hasFile($file)) {
            $file->setPost($this);
            $this->files->add($file);
        }

        return $this;
    }

    /**
     * Remove file
     *
     * @param PostFile $file
     *
     * @return Post
     */
    public function removeFile(PostFile $file)
    {
        if ($this->hasFile($file)) {
            $this->files->removeElement($file);
        }

        return $this;
    }

    /**
     * @return boolean
     */
    public function isUseOnSlider()
    {
        return $this->useOnSlider;
    }

    /**
     * @param boolean $useOnSlider
     * @return Post
     */
    public function setUseOnSlider($useOnSlider)
    {
        $this->useOnSlider = $useOnSlider;

        return $this;
    }



    /**
     * Is news?
     *
     * @return bool
     */
    public function isNews()
    {
        return $this->type == self::TYPE_NEWS;
    }

    /**
     * Is circular?
     *
     * @return bool
     */
    public function isCircular()
    {
        return $this->type == self::TYPE_CIRCULAR;
    }

    /**
     * On update
     */
    public function onUpdate()
    {
        $this->updatedAt = new \DateTime();
    }
}
