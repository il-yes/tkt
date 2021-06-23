<?php

namespace App\Entity;

use App\Repository\ResultRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ResultRepository::class)
 */
class Result
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ca;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $margin;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ebitda;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $loss;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $year;

    /**
     * @ORM\ManyToOne(targetEntity=Corporate::class, inversedBy="results")
     */
    private $corporate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCa(): ?int
    {
        return $this->ca;
    }

    public function setCa(?int $ca): self
    {
        $this->ca = $ca;

        return $this;
    }

    public function getMargin(): ?int
    {
        return $this->margin;
    }

    public function setMargin(?int $margin): self
    {
        $this->margin = $margin;

        return $this;
    }

    public function getEbitda(): ?int
    {
        return $this->ebitda;
    }

    public function setEbitda(?int $ebitda): self
    {
        $this->ebitda = $ebitda;

        return $this;
    }

    public function getLoss(): ?int
    {
        return $this->loss;
    }

    public function setLoss(?int $loss): self
    {
        $this->loss = $loss;

        return $this;
    }

    public function getYear(): ?\DateTimeInterface
    {
        return $this->year;
    }

    public function setYear(?\DateTimeInterface $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getCorporate(): ?Corporate
    {
        return $this->corporate;
    }

    public function setCorporate(?Corporate $corporate): self
    {
        $this->corporate = $corporate;

        return $this;
    }
}
