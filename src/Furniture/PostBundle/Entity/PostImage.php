<?php

namespace Furniture\PostBundle\Entity;

use Furniture\CommonBundle\Uploadable\UploadableInterface;
use Sylius\Component\Core\Model\Image;

class PostImage extends Image implements UploadableInterface
{
    /**
     * @var Post
     */
    private $post;

    /**
     * Set post
     *
     * @param Post $post
     *
     * @return PostImage
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
}
