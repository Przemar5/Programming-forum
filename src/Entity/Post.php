<?php

namespace App\Entity;

use App\Entity\Rating;
use App\Entity\Timestamps;
use App\Entity\SoftDelete;
use App\Repository\PostRepository;
use App\Repository\RatingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 */
class Post
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
     * @ORM\Column(type="text")
     * @Assert\Regex("/^[\w\S\- \!@#$%\^&\*\+=?\|,\.\/:;<>\{\}\(\)\[\]]+$/")
     */
    private $content;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Regex("/^[\w\S\- \!@#$%\^&\*\+=?\|,\.\/:;<>\{\}\(\)\[\]]+$/")
     */
    private $contentToAccept;

    /**
     * @ORM\Column(type="boolean", options={"default"=false})
     */
    private $accepted;

    /**
     * @ORM\Column(type="boolean", options={"default"=true}, nullable=true)
     */
    private $editAccepted;

    /**
     * @ORM\ManyToOne(targetEntity=Topic::class, inversedBy="posts")
     */
    private $topic;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="posts")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Rating::class, mappedBy="post", orphanRemoval=true)
     */
    private $ratings;

    public function __construct()
    {
        $this->ratings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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

    public function getTopic(): ?Topic
    {
        return $this->topic;
    }

    public function setTopic(?Topic $topic): self
    {
        $this->topic = $topic;

        return $this;
    }

    public function addRating(Rating $rating): self
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings[] = $rating;
            $rating->setPost($this);
        }

        return $this;
    }

    public function removeRating(Rating $rating): self
    {
        if ($this->ratings->contains($rating)) {
            $this->ratings->removeElement($rating);
            // set the owning side to null (unless already changed)
            if ($rating->getPost() === $this) {
                $rating->setPost(null);
            }
        }

        return $this;
    }

    public function isRatedBy(User $user, int $rateType): bool
    {
        return (bool) $this->ratings
            ->filter(fn($r) => $r->getUser()->getId() === $user->getId() && $r->getPoints() === $rateType)
            ->count();
    }

    public function isLikedBy(User $user): bool
    {
        return $this->isRatedBy($user, Rating::LIKE);
    }

    public function isDislikedBy(User $user): bool
    {
        return $this->isRatedBy($user, Rating::DISLIKE);
    }

    public function getPoints(): int
    {
        return array_reduce(
            $this->ratings->map(fn($r) => $r->getPoints())->toArray(), 
            fn($a, $b) => $a + $b,
            0
        );
    }

    public function getAccepted(): ?bool
    {
        return $this->accepted;
    }

    public function setAccepted(?bool $accepted): self
    {
        $this->accepted = $accepted;

        return $this;
    }

    public function getContentToAccept(): ?string
    {
        return $this->contentToAccept;
    }

    public function setContentToAccept(?string $contentToAccept): self
    {
        $this->contentToAccept = $contentToAccept;

        return $this;
    }

    public function getEditAccepted(): ?bool
    {
        return $this->editAccepted;
    }

    public function setEditAccepted(bool $editAccepted): self
    {
        $this->editAccepted = $editAccepted;

        return $this;
    }
}
