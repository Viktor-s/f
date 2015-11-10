<?php

namespace Furniture\PostBundle\Form\Type;

use Behat\Transliterator\Transliterator;
use Furniture\FactoryBundle\Entity\Factory;
use Furniture\PostBundle\Entity\Post;
use Furniture\PostBundle\Entity\PostFile;
use Furniture\PostBundle\Entity\PostImage;
use Sylius\Bundle\CoreBundle\Form\Type\ImageType;
use Furniture\PostBundle\Form\Type\FileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PostType extends AbstractType
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * Construct
     *
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('displayName', 'text', [
                'label' => 'post.form.display_name'
            ])
            ->add('slug', 'text', [
                'label' => 'post.form.slug',
                'required' => false
            ])
            ->add('type', 'choice', [
                'label' => 'post.form.type',
                'choices' => [
                    Post::TYPE_NEWS     => 'Post',
                    Post::TYPE_CIRCULAR => 'Circular'
                ]
            ])
            ->add('publishedAt', 'datetime', [
                'label' => 'post.form.published'
            ])
            ->add('factory', 'entity', [
                'class' => Factory::class,
                'required' => false,
                'label' => 'post.form.factory'
            ])
            ->add('images', 'collection', [
                'type' => new ImageType(PostImage::class),
                'allow_add' => true,
                'allow_delete' => true
            ])
            ->add('files', 'collection', [
                'type' => new FileType(PostFile::class),
                'allow_add' => true,
                'allow_delete' => true
            ])
            ->add('translations', 'a2lix_translationsForms', [
                'form_type' => new PostTranslationType()
            ]);

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            /** @var Post $post */
            $post = $event->getData();

            if (!$post->getCreator()) {
                $user = $this->tokenStorage->getToken()->getUser();
                $post->setCreator($user);
            }

            if (!$post->getSlug()) {
                $title = $post->getDisplayName();
                $slug = Transliterator::urlize($title);
                $post->setSlug($slug);
            }

            foreach ($post->getImages() as $image) {
                $image->setPost($post);
            }

            foreach ($post->getFiles() as $file) {
                $file->setPost($post);
            }
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'post';
    }
}
