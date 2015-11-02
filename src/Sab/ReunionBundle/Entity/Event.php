<?php

namespace Sab\ReunionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entité event
 *
 * @ORM\Table(name="event")
 * @ORM\Entity(repositoryClass="Sab\ReunionBundle\Repository\EventRepository")
 */
class Event
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
   
    
    /**
     *
     * @ORM\ManyToOne(targetEntity="Sab\ReunionBundle\Entity\User", inversedBy="eventAdmin", cascade={"remove", "persist"})
     */
    protected $userAdmin;
    
    
    /**
     *
     * @ORM\OneToOne(targetEntity="Sab\ReunionBundle\Entity\User", cascade={"remove", "persist"})
     */
    protected $userUser;
    
    
    /**
     *
     * @ORM\OneToMany(targetEntity="Sab\ReunionBundle\Entity\Question", mappedBy="event")
     */
    protected $question;

    
    
    /**
     *
     * @ORM\OneToMany(targetEntity="Sab\ReunionBundle\Entity\Document", mappedBy="event")
     */
    private $doc;
    
    
    /**
     *
     * @ORM\OneToOne(targetEntity="Sab\ReunionBundle\Entity\Theme", cascade={"remove", "persist"})
     */
    protected $theme;

    
     /**
     * @var string
     *
     * @ORM\Column(name="createur", type="string", length=255)
     */
    protected $createur;
    
    
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="label_event", type="string", length=255)
     */
    protected $labelEvent;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    protected $description;

    

    /**
     *
     * @ORM\Column(name="nbUserConnected", type="integer")
     */
    protected $nbUserConnected;
    
    
    
    
    public function __construct() {
        $this->nbUserConnected = 0;
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set labelEvent
     *
     * @param string $labelEvent
     * @return Event
     */
    public function setLabelEvent($labelEvent)
    {
        $this->labelEvent = $labelEvent;

        return $this;
    }

    /**
     * Get labelEvent
     *
     * @return string 
     */
    public function getLabelEvent()
    {
        return $this->labelEvent;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Event
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set nbUserConnected
     *
     * @param integer $nbUserConnected
     * @return Event
     */
    public function setNbUserConnected($nbUserConnected)
    {
        $this->nbUserConnected = $nbUserConnected;

        return $this;
    }

    /**
     * Get nbUserConnected
     *
     * @return integer 
     */
    public function getNbUserConnected()
    {
        return $this->nbUserConnected;
    }

    /**
     * Set userAdmin
     *
     * @param \Sab\ReunionBundle\Entity\User $userAdmin
     * @return Event
     */
    public function setUserAdmin(\Sab\ReunionBundle\Entity\User $userAdmin = null)
    {
        $this->userAdmin = $userAdmin;

        return $this;
    }

    /**
     * Get userAdmin
     *
     * @return \Sab\ReunionBundle\Entity\User 
     */
    public function getUserAdmin()
    {
        return $this->userAdmin;
    }

    /**
     * Add question
     *
     * @param \Sab\ReunionBundle\Entity\Question $question
     * @return Event
     */
    public function addQuestion(\Sab\ReunionBundle\Entity\Question $question)
    {
        $this->question[] = $question;

        return $this;
    }

    /**
     * Remove question
     *
     * @param \Sab\ReunionBundle\Entity\Question $question
     */
    public function removeQuestion(\Sab\ReunionBundle\Entity\Question $question)
    {
        $this->question->removeElement($question);
    }

    /**
     * Get question
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set userUser
     *
     * @param \Sab\ReunionBundle\Entity\User $userUser
     * @return Event
     */
    public function setUserUser(\Sab\ReunionBundle\Entity\User $userUser = null)
    {
        $this->userUser = $userUser;

        return $this;
    }

    /**
     * Get userUser
     *
     * @return \Sab\ReunionBundle\Entity\User 
     */
    public function getUserUser()
    {
        return $this->userUser;
    }

    /**
     * Add doc
     *
     * @param \Sab\ReunionBundle\Entity\Document $doc
     * @return Event
     */
    public function addDoc(\Sab\ReunionBundle\Entity\Document $doc)
    {
        $this->doc[] = $doc;
        return $this;
    }

    /**
     * Remove doc
     *
     * @param \Sab\ReunionBundle\Entity\Document $doc
     */
    public function removeDoc(\Sab\ReunionBundle\Entity\Document $doc)
    {
        $this->doc->removeElement($doc);
    }

    /**
     * Get doc
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDoc()
    {
        return $this->doc;
    }

    /**
     * Set theme
     *
     * @param \Sab\ReunionBundle\Entity\Theme $theme
     * @return Event
     */
    public function setTheme(\Sab\ReunionBundle\Entity\Theme $theme = null)
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Get theme
     *
     * @return \Sab\ReunionBundle\Entity\Theme 
     */
    public function getTheme()
    {
        return $this->theme;
    }
    

    /**
     * Set createur
     *
     * @param string $createur
     * @return Event
     */
    public function setCreateur($createur)
    {
        $this->createur = $createur;

        return $this;
    }

    /**
     * Get createur
     *
     * @return string 
     */
    public function getCreateur()
    {
        return $this->createur;
    }
}
