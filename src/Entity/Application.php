<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ApplicationRepository")
 */
class Application {

  /**
   * @ORM\Id()
   * @ORM\GeneratedValue()
   * @ORM\Column(type="integer")
   */
  private $id;

  /**
   * @ORM\ManyToOne(targetEntity="App\Entity\Advert")
   * @ORM\JoinColumn(nullable=false)
   */
  private $advert;


  /**
   * @ORM\Column(type="string", length=255)
   */
  private $author;

  /**
   * @ORM\Column(type="text")
   */
  private $content;

  /**
   * @ORM\Column(type="datetime")
   */
  private $date;


  public function __construct(string $author, string $msg) {
    $this->date = new DateTime();
    $this->author = $author;
    $this->content = $msg;
  }


  public function getId(): ?int {
    return $this->id;
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

  public function getDate(): ?DateTimeInterface {
    return $this->date;
  }

  public function setDate(DateTimeInterface $date): self {
    $this->date = $date;

    return $this;
  }

  /**
   * @return \App\Entity\Advert
   */
  public function getAdvert(): Advert {
    return $this->advert;
  }

  /**
   * @param \App\Entity\Advert $advert
   */
  public function setAdvert(Advert $advert): void {
    $this->advert = $advert;
  }

}
