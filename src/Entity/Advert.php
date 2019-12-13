<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdvertRepository")
 * @ORM\Table(name="oc_advert")
 * @ORM\HasLifecycleCallbacks()
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

  /**
   * @ORM\ManyToMany(targetEntity="App\Entity\Category", inversedBy="adverts", cascade={"persist"})
   */
  private $categories;

  /**
   * @ORM\OneToMany(targetEntity="App\Entity\Application", mappedBy="advert")
   */
  private $applications;

  /**
   * @ORM\Column(type="datetime", nullable=true)
   */
  private $updatedAt;

  /**
   * @ORM\Column(type="integer")
   */
  private $nbApplications;


  public function __construct(string $title = 'NOTHING', string $author = 'UNKNOWN', string $content = 'NOTHING') {
    $this->date = new DateTime();
    $this->published = true;
    $this->setTitle($title);
    $this->setAuthor($author);
    $this->setContent($content);
    $this->categories = new ArrayCollection();
    $this->applications = new ArrayCollection();
    $this->setNbApplications(0);
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

  public function getImage() : ?Image {
    return $this->image;
  }

  /**
   * @return Collection|Category[]
   */
  public function getCategories(): Collection
  {
      return $this->categories;
  }

  public function addCategory(Category $category): self
  {
      if (!$this->categories->contains($category)) {
          $this->categories[] = $category;
      }

      return $this;
  }

  public function removeCategory(Category $category): self
  {
      if ($this->categories->contains($category)) {
          $this->categories->removeElement($category);
      }

      return $this;
  }

  /**
   * @return Collection|Application[]
   */
  public function getApplications(): Collection
  {
      return $this->applications;
  }

  public function addApplication(Application $application): self
  {
      if (!$this->applications->contains($application)) {
          $this->applications[] = $application;
          $application->setAdvert($this);
      }

      return $this;
  }

  public function removeApplication(Application $application): self
  {
      if ($this->applications->contains($application)) {
          $this->applications->removeElement($application);
          // set the owning side to null (unless already changed)
          if ($application->getAdvert() === $this) {
              $application->setAdvert(null);
          }
      }

      return $this;
  }

  public function getUpdatedAt(): ?\DateTimeInterface
  {
      return $this->updatedAt;
  }

  public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
  {
      $this->updatedAt = $updatedAt;

      return $this;
  }

    /**
     * @throws \Exception
     * @ORM\PreUpdate()
     */
  public function updateDate() {
      $this->setUpdatedAt(new DateTime());
  }

  public function getNbApplications(): ?int
  {
      return $this->nbApplications;
  }

  // Utilisé juste pour l'initialisation
  private function setNbApplications(int $nbApplications): self
  {
      $this->nbApplications = $nbApplications;

      return $this;
  }

  public function increaseApplicationNb() {
      $this->nbApplications++;
  }

  public function decreaseApplicationNb() {
      $this->nbApplications--;
  }

}
