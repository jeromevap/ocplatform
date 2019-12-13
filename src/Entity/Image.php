<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 */
class Image {

  /**
   * @ORM\Id()
   * @ORM\GeneratedValue()
   * @ORM\Column(type="integer")
   */
  private $id;

  /**
   * @ORM\Column(type="string", length=255, nullable=true)
   */
  private $url;

  /**
   * @ORM\Column(type="string", length=255, nullable=true)
   */
  private $alt;

  public function __construct(string $url = null, string $alt = null) {
    $this->url = $url;
    $this->alt = $alt;
  }

  public function getId(): ?int {
    return $this->id;
  }

  public function getUrl(): ?string {
    return $this->url;
  }

  public function setUrl(?string $url): self {
    $this->url = $url;

    return $this;
  }

  public function getAlt(): ?string {
    return $this->alt;
  }

  public function setAlt(?string $alt): self {
    $this->alt = $alt;

    return $this;
  }

    private $file;

    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    public function upload()
    {
        // Si jamais il n'y a pas de fichier (champ facultatif), on ne fait rien
        if (is_null($this->file)) {
            return;
        }

        // On récupère le nom original du fichier de l'internaute
        $name = $this->file->getClientOriginalName();

        // On déplace le fichier envoyé dans le répertoire de notre choix
        $this->file->move($this->getUploadRootDir(), $name);

        // On sauvegarde le nom de fichier dans notre attribut $url
        $this->url = $name;

        // On crée également le futur attribut alt de notre balise <img>
        $this->alt = $name;
    }

    public function getUploadDir()
    {
        // On retourne le chemin relatif vers l'image pour un navigateur (relatif au répertoire /web donc)
        return 'uploads/img';
    }

    protected function getUploadRootDir()
    {
        // On retourne le chemin relatif vers l'image pour notre code PHP
        return __DIR__.'/../../../../htdocs/'.$this->getUploadDir();
    }

}
