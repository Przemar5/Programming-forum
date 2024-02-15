<?php

namespace App\Entity;

use App\Entity\Post;
use App\Entity\Timestamps;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
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
     * @Assert\Length(max=255)
     * @Assert\Regex("/^[\w\- \!@#$%\^&\*\+=?\|,\.\/:;<>\{\}\(\)\[\]]+$/")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     * @Assert\Regex("/^[\w\-]+$/")
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Regex("/^[\w\- \!@#$%\^&\*\+=?\|,\.\/:;<>\{\}\(\)\[\]]+$/")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="subcategories")
     */
    private $parentCategory;

    /**
     * @ORM\OneToMany(targetEntity=Category::class, mappedBy="parentCategory")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=true)
     * @ORM\OrderBy({"id"="ASC"})
     */
    private $subcategories;

    /**
     * @ORM\OneToMany(targetEntity=Topic::class, mappedBy="category")
     * @ORM\JoinColumn(nullable=false)
     */
    private $topics;

    public function __construct()
    {
        $this->topics = new ArrayCollection();
        $this->subcategories = new ArrayCollection();
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    // For other types' forms handling
    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return Collection|Topic[]
     */
    public function getTopics(): Collection
    {
        $iterator = $this->topics->getIterator();
        $iterator->uasort(fn($a, $b) => ($a->getId() < $b->getId()) ? 1 : -1);
        
        return new ArrayCollection(iterator_to_array($iterator));
    }

    public function addTopic(Topic $topic): self
    {
        if (!$this->topics->contains($topic)) {
            $this->topics[] = $topic;
            $topic->setCategory($this);
        }

        return $this;
    }

    public function removeTopic(Topic $topic): self
    {
        if ($this->topics->contains($topic)) {
            $this->topics->removeElement($topic);
            // set the owning side to null (unless already changed)
            if ($topic->getCategory() === $this) {
                $topic->setCategory(null);
            }
        }

        return $this;
    }

    public function getParentCategory(): ?self
    {
        return $this->parentCategory;
    }

    public function setParentCategory(?self $parentCategory): self
    {
        $this->parentCategory = $parentCategory;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getSubcategories(): Collection
    {
        return $this->subcategories;
    }

    public function addSubcategory(Category $subcategory): self
    {
        if (!$this->subcategories->contains($subcategory)) {
            $this->subcategories[] = $subcategory;
            $subcategory->setParentCategory($this);
        }

        return $this;
    }

    public function removeSubcategory(Category $subcategory): self
    {
        if ($this->subcategories->contains($subcategory)) {
            $this->subcategories->removeElement($subcategory);
            // set the owning side to null (unless already changed)
            if ($subcategory->getParentCategory() === $this) {
                $subcategory->setParentCategory(null);
            }
        }

        return $this;
    }

    public function topicsCount(): int
    {
        $count = $this->topics->count();

        foreach ($this->subcategories as $subcategory) {
            $count += $subcategory->topicsCount();
        }

        return $count;
    }

    public function postsCount(): int
    {
        $count = 0;

        foreach ($this->getSubcategoriesAndTopics() as $entity) {
            $count = $entity->postsCount();
        }

        return $count;
    }

    public function lastPost(): ?Post
    {
        $last = null;

        foreach ($this->getSubcategoriesAndTopics() as $entity) {
            $candidate = $entity->lastPost();
            
            if ($last === null || ($candidate && $candidate->getCreatedAt() > $last->getCreatedAt())) {
                $last = $candidate;
            }
        }

        return $last;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    private function getSubcategoriesAndTopics(): array
    {
        return array_merge($this->subcategories->toArray(), $this->topics->toArray());
    }
}
