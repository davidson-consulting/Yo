<?php

namespace Sab\ReunionBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité User
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Sab\ReunionBundle\Repository\UserRepository")
 */
class User extends BaseUser {

    private static $algo_crypt = MCRYPT_RIJNDAEL_128;
    private static $key = "davidson séminaire";
    private static $mode = "cbc";

    /**
     * Id de l'utilisateur
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Nom d'utilisateur
     * @var string
     */
    protected $username;

    /**
     * Email d'utilisateur
     * @var string
     */
    protected $email;

    /**
     * Activer ou désactiver le compte utilisateur
     * @var boolean
     */
    protected $enabled;

    /**
     * Mot de passe du compte utilisateur 
     * Encrypted password. Must be persisted.
     *
     * @var string
     */
    protected $password;

    /**
     *
     * Mot de passe decrypte du compte utilisateur
     * @var type string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $password_decrypte;

    /**
     * Date d'expiration du compte
     * @var Date time
     */
    protected $expiresAt;

    /**
     * Date début d'événement
     * @var Date time
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $date_debut_event;

    /**
     * Date de fin d'événement
     * @var Date time
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $date_fin_event;

    /**
     * Relation entre Admin et l'événemnt
     * @ORM\OneToMany(targetEntity="Sab\ReunionBundle\Entity\Event", mappedBy="userAdmin")
     */
    protected $eventAdmin;

    /**
     * Relation entre User et l'événement
     * @ORM\OneToOne(targetEntity="Sab\ReunionBundle\Entity\Event", mappedBy="userUser", cascade={"remove"})
     */
    protected $eventUser;

    public function __construct() {
        parent::__construct();
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
     * Get expiresAt
     * @return Date Time
     */
    public function getExpiresAt() {
        return $this->expiresAt;
    }
    
    /**
     * Get userName
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }
    
    /**
     * Get email
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }
    
    /**
     * Get password
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }
    
    /**
     * Set userName
     * @param string $username
     * @return \Sab\ReunionBundle\Entity\User
     */
    public function setUsername($username) {
        $this->username = $username;

        return $this;
    }
    
    /**
     * set email
     * @param string $email
     * @return \Sab\ReunionBundle\Entity\User
     */
    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }
    
    /**
     * set password
     * @param string $password
     * @return \Sab\ReunionBundle\Entity\User
     */
    public function setPassword($password) {
        $this->password = $password;

        return $this;
    }

    /**
     * Ajouter un evenement admin
     *
     * @param \Sab\ReunionBundle\Entity\Event $eventAdmin
     * @return User
     */
    public function addEventAdmin(\Sab\ReunionBundle\Entity\Event $eventAdmin) {
        $this->eventAdmin[] = $eventAdmin;

        return $this;
    }

    /**
     * Supprimer un événement admin
     * Remove eventAdmin
     *
     * @param \Sab\ReunionBundle\Entity\Event $eventAdmin
     */
    public function removeEventAdmin(\Sab\ReunionBundle\Entity\Event $eventAdmin) {
        $this->eventAdmin->removeElement($eventAdmin);
    }

    /**
     * Get eventAdmin
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEventAdmin() {
        return $this->eventAdmin;
    }

    /**
     * Ajouter un événement user
     *
     * @param \Sab\ReunionBundle\Entity\Event $eventUser
     * @return User
     */
    public function addEventUser(\Sab\ReunionBundle\Entity\Event $eventUser) {
        $this->eventUser[] = $eventUser;

        return $this;
    }

    /**
     * Supprimer un événement user
     *
     * @param \Sab\ReunionBundle\Entity\Event $eventUser
     */
    public function removeEventUser(\Sab\ReunionBundle\Entity\Event $eventUser) {
        $this->eventUser->removeElement($eventUser);
    }

    /**
     * Get eventUser
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEventUser() {
        return $this->eventUser;
    }

    /**
     * Set eventUser
     *
     * @param \Sab\ReunionBundle\Entity\Event $eventUser
     * @return User
     */
    public function setEventUser(\Sab\ReunionBundle\Entity\Event $eventUser = null) {
        $this->eventUser = $eventUser;

        return $this;
    }

    /**
     * Set date_debut_event
     *
     * @param \DateTime $dateDebutEvent
     * @return User
     */
    public function setDateDebutEvent($dateDebutEvent) {
        $this->date_debut_event = $dateDebutEvent;

        return $this;
    }

    /**
     * Get date_debut_event
     *
     * @return \DateTime 
     */
    public function getDateDebutEvent() {
        return $this->date_debut_event;
    }

    /**
     * Set date_fin_event
     *
     * @param \DateTime $dateFinEvent
     * @return User
     */
    public function setDateFinEvent($dateFinEvent) {
        $this->date_fin_event = $dateFinEvent;

        return $this;
    }
    
    
    /**
     * Get enabled
     * @return boolean
     */
    public function isEnabled() {
        return $this->enabled;
    }

    /**
     * Set enabled
     * @param boolean $boolean
     * @return \Sab\ReunionBundle\Entity\User
     */
    public function setEnabled($boolean) {
        $this->enabled = (Boolean) $boolean;

        return $this;
    }

    /**
     * Get date_fin_event
     *
     * @return \DateTime 
     */
    public function getDateFinEvent() {
        return $this->date_fin_event;
    }

    /**
     * Set password_decrypte
     *
     * @param string $passwordDecrypte
     * @return User
     */
    public function setPasswordDecrypte($passwordDecrypte) {
        $this->password_decrypte = $passwordDecrypte;

        return $this;
    }

    /**
     * Get password_decrypte
     *
     * @return string 
     */
    public function getPasswordDecrypte() {
        return $this->password_decrypte;
    }

    /**
     * Crypter un mot de passe en md5
     * @param string $data
     * @return string
     */
    public static function crypt($data) {
        $keyHash = md5(self::$key);
        $key = substr($keyHash, 0, mcrypt_get_key_size(self::$algo_crypt, self::$mode));
        $iv = substr($keyHash, 0, mcrypt_get_block_size(self::$algo_crypt, self::$mode));
        $data = mcrypt_encrypt(self::$algo_crypt, $key, $data, self::$mode, $iv);
        return base64_encode($data);
    }
    
    /**
     * Decrypter un mot de passe crypter en md5
     * @param string $data
     * @return string
     */
    public static function decrypt($data) {
        $keyHash = md5(self::$key);
        $key = substr($keyHash, 0, mcrypt_get_key_size(self::$algo_crypt, self::$mode));
        $iv = substr($keyHash, 0, mcrypt_get_block_size(self::$algo_crypt, self::$mode));
        $data = base64_decode($data);
        $data = mcrypt_decrypt(self::$algo_crypt, $key, $data, self::$mode, $iv);
        return rtrim($data);
    }

}
