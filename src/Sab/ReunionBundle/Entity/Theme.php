<?php

namespace Sab\ReunionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entité thème
 *
 * @ORM\Table(name="theme")
 * @ORM\Entity
 */
class Theme {

    /**
     * Id du thème
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Nom de l'image de profile
     * @var string
     *
     * @ORM\Column(name="profil_picture", type="string", length=255)
     */
    private $profilPicture;

    /**
     * Image de profile télécharger 
     * @Assert\File(
     *          maxSize = "1024k",
     *          mimeTypes = {"image/jpeg", "image/png", "image/gif"}, 
     *          mimeTypesMessage = "Votre image est volumineuse"
     *          )
     */
    protected $fileProfilPicture;

    
    /**
     * Nom du logo
     * @var string
     *
     * @ORM\Column(name="logo", type="string", length=255)
     */
    private $logo;

    
    /**
     * logo télécharger
     * @Assert\File(
     *          maxSize = "1024k",
     *          mimeTypes = {"image/jpeg", "image/png", "image/gif"},          
     *          mimeTypesMessage = "Votre image est volumineuse"
     * )
     */
    protected $fileLogo;

    /**
     * Image de fond 
     * @var string
     *
     * @ORM\Column(name="background_picture", type="string", length=255)
     */
    private $backgroundPicture;

    /**
     * Image de fond télécharger
     * @Assert\File(
     *          maxSize = "1024k",
     *          mimeTypes = {"image/jpeg", "image/png", "image/gif"}, 
     *          mimeTypesMessage = "Votre image est volumineuse")
     */
    protected $fileBackground;
    
    const BANNERS = "banners";
    const LOGO = "logo";
    const BACKGROUND = "background";
    
    /**
     * Chemin de l'image
     * @return string => path
     */
    public function getFullfileProfilPicture() {
        return null == $this->fileProfilPicture ? null : $this->getWebPath();
    }
    
    /**
     * Chemin du le dossier web
     * @return string => path du dossier web
     */
    public function getWebPath() {
        return __DIR__ . "/../../../../web";
        //return __DIR__ . "\..\..\..\..\web";
    }
    
    /**
     * Chemin du dossier des images télécharger
     * 
     * @param Event $id_event
     * @param string $name => ("Banners" || "Logo" || "Backgroud")
     * @return string => path de l'image 
      */
    public function getUploadRootDir($id_event, $name) {
        if ($name == self::BANNERS) {
            return $this->getWebPath() . "/doc/event_" . $id_event . "/banners";
        }
        if ($name == self::LOGO) {
            return $this->getWebPath() . "/doc/event_" . $id_event . "/logo";
        }
        if ($name == self::BACKGROUND) {
            return $this->getWebPath() . "/doc/event_" . $id_event . "/background";
        }
    }
    
    /**
     * Chemin complet du l'image de profile
     * 
     * @param Event $id_event
     * @return string => path profilPicture
     */
    public function getPathFileProfil($id_event) {
        return "doc/event_" . $id_event . "/banners/" . $this->profilPicture;
    }
    
    /**
     * le chemin complet de l'image logo
     * 
     * @param Event $id_event
     * @return string => path logo
     */
    public function getPathFileLogo($id_event) {
        return "doc/event_" . $id_event . "/logo/" . $this->logo;
    }
    
    
    /**
     * le chemin complet de l'image de fond
     * 
     * @param Event $id_event
     * @return string => path l'image de fond 
     */
    public function getPathFileBackground($id_event) {
        return "doc/event_" . $id_event . "/background/" . $this->backgroundPicture;
    }
    
    
    /**
     * Action upload file
     * 
     * @param Event $id_event
     * @param string $statut => Ajouter une image ou modifier ("add" || "edit")
     */
    public function uploadFile($id_event, $statut) {
            if ($id_event != null && $statut === "add") {
                $this->fileProfilPicture->move($this->getUploadRootDir($id_event, self::BANNERS), $this->fileProfilPicture->getClientOriginalName());
                $this->fileLogo->move($this->getUploadRootDir($id_event, self::LOGO), $this->fileLogo->getClientOriginalName());
                $this->fileBackground->move($this->getUploadRootDir($id_event, self::BACKGROUND), $this->fileBackground->getClientOriginalName());
            }
            if ($id_event != null && $statut === "edit") {
                $this->getFileProfilPicture() !== null ? ($this->getFileProfilPicture()->move($this->getUploadRootDir($id_event, self::BANNERS), $this->getFileProfilPicture()->getClientOriginalName())) : null;
                $this->getFileLogo() !== null ? ($this->getFileLogo()->move($this->getUploadRootDir($id_event, self::LOGO), $this->getFileLogo()->getClientOriginalName())) : null;
                $this->getFileBackground() !== null ? ($this->getFileBackground()->move($this->getUploadRootDir($id_event, self::BACKGROUND), $this->getFileBackground()->getClientOriginalName())) : null;

                $this->fileProfilPicture !== null ? ($this->setProfilPicture($this->getFileProfilPicture()->getClientOriginalName())) : null;
                $this->fileLogo !== null ? ($this->setLogo($this->getFileLogo()->getClientOriginalName())) : null;
                $this->fileBackground !== null ? ($this->setBackgroundPicture($this->getFileBackground()->getClientOriginalName())) : null;
            }
            if ($id_event == null) {
                $this->setProfilPicture($this->fileProfilPicture->getClientOriginalName());
                $this->setLogo($this->fileLogo->getClientOriginalName());
                $this->setBackgroundPicture($this->fileBackground->getClientOriginalName());
            }
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * set fileLogo
     * 
     * @param File $fileLogo
     * @return File 
     */
    public function setFileLogo($fileLogo) {
        return $this->fileLogo = $fileLogo;
    }
    
    /**
     * 
     * set fileProfilPicture
     * 
     * @param File $fileProfilPicture
     * @return File
     */
    public function setFileProfilPicture($fileProfilPicture) {
        return $this->fileProfilPicture = $fileProfilPicture;
    }

    /**
     * set fileBackground
     * 
     * @param File $fileBackground
     * @return File
     */
    public function setFileBackground($fileBackground) {
        return $this->fileBackground = $fileBackground;
    }
    
    /**
     * Get fileLogo
     * 
     * @return File
     */
    public function getFileLogo() {
        return $this->fileLogo;
    }

    /**
     * Get fileProfilPicture
     * 
     * @return File
     */
    public function getFileProfilPicture() {
        return $this->fileProfilPicture;
    }

    /**
     * Get fileBackground
     * 
     * @return File
     */
    public function getFileBackground() {
        return $this->fileBackground;
    }

    /**
     * Set profilPicture
     *
     * @param string $profilPicture
     * @return Theme
     */
    public function setProfilPicture($profilPicture) {
        $this->profilPicture = $profilPicture;

        return $this;
    }

    /**
     * Get profilPicture
     *
     * @return string 
     */
    public function getProfilPicture() {
        return $this->profilPicture;
    }

    /**
     * Set logo
     *
     * @param string $logo
     * @return Theme
     */
    public function setLogo($logo) {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return string 
     */
    public function getLogo() {
        return $this->logo;
    }

    /**
     * Set backgroundPicture
     *
     * @param string $backgroundPicture
     * @return Theme
     */
    public function setBackgroundPicture($backgroundPicture) {
        $this->backgroundPicture = $backgroundPicture;

        return $this;
    }

    /**
     * Get backgroundPicture
     *
     * @return string 
     */
    public function getBackgroundPicture() {
        return $this->backgroundPicture;
    }

}
