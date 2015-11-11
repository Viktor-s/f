<?php

namespace Furniture\PostBundle\Entity;

use Furniture\CommonBundle\Uploadable\UploadableInterface;
use Sylius\Component\Core\Model\Image;

class PostFile extends Image implements UploadableInterface
{
    /**
     * @var Post
     */
    private $post;

    /**
     *
     * @var string
     */
    private $name;
    
    public function doSetOriginName()
    {
        if($this->getFile() instanceof \SplFileInfo ){
            $this->setName($this->getFile()->getFilename());
        }
    }
    
    /**
     * Set post
     *
     * @param Post $post
     *
     * @return PostFile
     */
    public function setPost(Post $post)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get post
     *
     * @return Post
     */
    public function getPost()
    {
        return $this->post;
    }
    
    /**
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * 
     * @param string $name
     * @return \Furniture\PostBundle\Entity\PostFile
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
}
