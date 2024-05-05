<?php

namespace App\Entity;

use App\Entity\Post;
use App\Entity\Timestamps;
use App\Entity\SoftDelete;
use App\Repository\TopicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TopicRepository::class)
 */
class Topic
{
    use Timestamps;
    use SoftDelete;
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(min=8, max=255)
     * @Assert\Regex("/^[\w\- \!@#$%\^&\*\+=?\|,\.\/:;<>\{\}\(\)\[\]]+$/")
     */
    private $name;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $accepted = false;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="topics")
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="topic")
     */
    private $posts;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class)
     */
    private $tags;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="topics")
     */
    private $user;

    /**
     * @ORM\Column(type="boolean", options={"defaults"=0}, nullable=true)
     */
    private $closed = false;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setTopic($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getTopic() === $this) {
                $post->setTopic(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function postsCount(): int
    {
        return $this->posts->count();
    }

    public function lastPost(): ?Post
    {
        $last = null;

        foreach ($this->posts as $post) {
            if ($last === null || ($post->getCreatedAt() > $last->getCreatedAt())) {
                $last = $post;
            }
        }

        return $last;
    }

    public function getClosed(): ?bool
    {
        return $this->closed;
    }

    public function setClosed(bool $closed): self
    {
        $this->closed = $closed;

        return $this;
    }

    public function getAccepted(): ?bool
    {
        return $this->accepted;
    }

    public function setAccepted(bool $accepted): self
    {
        $this->accepted = $accepted;

        return $this;
    }

}
