<?php

namespace App\Entity;

use App\Repository\PhoneRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\ExclusionPolicy;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @ORM\Entity(repositoryClass=PhoneRepository::class)
 *
 * @ExclusionPolicy("all")
 *
 * @Hateoas\Relation(
 *     "self",
 *     href = @Hateoas\Route(
 *     "app_phone_show",
 *     parameters = { "id" = "expr(object.getId())" },
 *     absolute= true
 *     )
 * )
 *
 * @Hateoas\Relation(
 *     "list",
 *     href= @Hateoas\Route(
 *     "app_phone_list",
 *     absolute = true
 *     )
 * )
 */
class Phone
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Serializer\Expose()
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Serializer\Expose()
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Serializer\Expose()
     */
    private $description;

    /**
     * @ORM\Column(type="float")
     *
     * @Serializer\Expose()
     */
    private $price;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }
}
