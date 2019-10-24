<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdvertRepository")
 * @ORM\Table(name="oc_advert")
 */
class Advert {

  /**
   * @ORM\Id()
   * @ORM\GeneratedValue()
   * @ORM\Column(type="integer")
   */
  private $id;

  /**
   * @ORM\Column(type="datetime")
   */
  private $date;

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $title;

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $author;

  /**
   * @ORM\Column(type="text")
   */
  private $content;

  /**
   * @ORM\Column(type="boolean")
   */
  private $published;

  /**
   * @ORM\OneToOne(targetEntity="App\Entity\Image", cascade={"persist"})
   * @ORM\JoinColumn(nullable=true)
   * La deuxième clause est optionnelle true par défaut)
   */
  private $image;


  public function __construct(string $title = 'NOTHING', string $author = 'UNKNOWN', string $content = 'NOTHING') {
    $this->date = new DateTime();
    $this->published = TRUE;
    $this->setTitle($title);
    $this->setAuthor($author);
    $this->setContent($content);
  }

  public function getId(): ?int {
    return $this->id;
  }

  public function getDate(): ?DateTimeInterface {
    return $this->date;
  }

  public function setDate(DateTimeInterface $date): self {
    $this->date = $date;

    return $this;
  }

  public function getTitle(): ?string {
    return $this->title;
  }

  public function setTitle(string $title): self {
    $this->title = $title;

    return $this;
  }

  public function getAuthor(): ?string {
    return $this->author;
  }

  public function setAuthor(string $author): self {
    $this->author = $author;

    return $this;
  }

  public function getContent(): ?string {
    return $this->content;
  }

  public function setContent(string $content): self {
    $this->content = $content;

    return $this;
  }

  public function getPublished(): ?bool {
    return $this->published;
  }

  public function setPublished(bool $published): self {
    $this->published = $published;

    return $this;
  }

  public function setImage(Image $image = NULL) {
    $this->image = $image;
  }

  public function getImage() {
    return $this->image;
  }

}
