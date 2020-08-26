<?php

namespace App\Entity;

trait SoftDelete
{
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deleted_at;


    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deleted_at;
    }

    public function setDeletedAt()
    {
        $this->deleted_at = new \DateTime();
    }
}
