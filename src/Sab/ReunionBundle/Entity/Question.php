<?php

namespace Sab\ReunionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entité question
 *
 * @ORM\Table(name="question")
 * @ORM\Entity(repositoryClass="Sab\ReunionBundle\Repository\QuestionRepository")
 */
class Question
{
    /**
     * id question
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    
    /**
     *
     * @ORM\ManyToOne(targetEntity="Sab\ReunionBundle\Entity\Event")
     */
    protected $event;
    
    
    /**
     * Contenu de la question
     * @var string
     *
     * @ORM\Column(name="contenu", type="string", length=32768)
     */
    protected $contenu;


    /**
     *
     *@ORM\OneToMany(targetEntity="Sab\ReunionBundle\Entity\Commentaire", mappedBy="question", cascade={"persist","remove"})
     *
     */
    protected $commentaire;
   
    /**
     * Auteur de la question  
     * @var string
     * 
     * @ORM\Column(name="auteur", type="string", length=255, nullable=true)
     */
    protected $auteur;

    /**
     *
     * Nombre de likes
     * @var integer
     * 
     * @ORM\Column(name="nbLike", type="integer") 
     */
    protected $nbLike;
    
    
    
    /**
     * Nombre de dislikes
     * @var integer
     * @ORM\Column(name="nbDislike", type="integer")
     */
    protected $nbDislike;
    
    /**
     * Date de publication de la question
     * @var string
     * @ORM\Column(name="datePublication", type="datetime")
     */
    protected $datePublication;

    
    /**
     *  
     * @var boolean
     * @ORM\Column(name="isfocus", type="boolean")
     */
    protected $isfocus;

    /**
     *@var string
     *@ORM\Column(name="flagDeleted", type="boolean", options={"default":false})
     *
     *
     */
    protected $flagDeleted;

    
    public function __construct() {
        $this->datePublication = new \DateTime();
        $this->nbLike = 0;
        $this->nbDislike = 0;
        $this->isfocus = false;
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
     * Set contenu
     *
     * @param string $contenu
     * @return Question
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;

        return $this;
    }

    /**
     * Get contenu
     *
     * @return string 
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * Set auteur
     *
     * @param string $auteur
     * @return Question
     */
    public function setAuteur($auteur)
    {
        $this->auteur = $auteur;

        return $this;
    }

    /**
     * Get auteur
     *
     * @return string 
     */
    public function getAuteur()
    {
        return $this->auteur;
    }

    /**
     * Set nbLike
     *
     * @param integer $nbLike
     * @return Question
     */
    public function setNbLike($nbLike)
    {
        $this->nbLike = $nbLike;

        return $this;
    }

    /**
     * Get nbLike
     *
     * @return integer 
     */
    public function getNbLike()
    {
        return $this->nbLike;
    }

    /**
     * Set datePublication
     *
     * @param string $datePublication
     * @return Question
     */
    public function setDatePublication($datePublication)
    {
        $this->datePublication = $datePublication;

        return $this;
    }

    /**
     * Get datePublication
     *
     * @return string 
     */
    public function getDatePublication()
    {
        return $this->datePublication;
    }

    

    /**
     * Set event
     *
     * @param \Sab\ReunionBundle\Entity\Event $event
     * @return Question
     */
    public function setEvent(\Sab\ReunionBundle\Entity\Event $event = null)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return \Sab\ReunionBundle\Entity\Event 
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set nbDislike
     *
     * @param integer $nbDislike
     * @return Question
     */
    public function setNbDislike($nbDislike)
    {
        $this->nbDislike = $nbDislike;

        return $this;
    }

    /**
     * Get nbDislike
     *
     * @return integer 
     */
    public function getNbDislike()
    {
        return $this->nbDislike;
    }
    
    /**
     * Get focus
     * @return boolean
     */
    public function getIsfocus(){
        return $this->isfocus;
    }
    
    /**
     * Set focus
     * @param boolean $focus
     * @return boolean
     */
    public function setIsfocus($focus){
        return $this->isfocus = $focus;
    }
    

    /**
     * Add commentraire
     *
     * @param \Sab\ReunionBundle\Entity\Commentaire $commentraire
     * @return Question
     */
    public function addCommentraire(\Sab\ReunionBundle\Entity\Commentaire $commentraire)
    {
        $this->commentraire[] = $commentraire;

        return $this;
    }

    /**
     * Remove commentraire
     *
     * @param \Sab\ReunionBundle\Entity\Commentaire $commentraire
     */
    public function removeCommentraire(\Sab\ReunionBundle\Entity\Commentaire $commentraire)
    {
        $this->commentraire->removeElement($commentraire);
    }

    /**
     * Get commentraire
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCommentraire()
    {
        return $this->commentraire;
    }

    /**
     * Add commentaire
     *
     * @param \Sab\ReunionBundle\Entity\Commentaire $commentaire
     * @return Question
     */
    public function addCommentaire(\Sab\ReunionBundle\Entity\Commentaire $commentaire)
    {
        $this->commentaire[] = $commentaire;

        return $this;
    }

    /**
     * Remove commentaire
     *
     * @param \Sab\ReunionBundle\Entity\Commentaire $commentaire
     */
    public function removeCommentaire(\Sab\ReunionBundle\Entity\Commentaire $commentaire)
    {
        $this->commentaire->removeElement($commentaire);
    }

    /**
     * Get commentaire
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * Set flagDeleted
     *
     * @param string $flagDeleted
     * @return Question
     */
    public function setFlagDeleted($flagDeleted)
    {
        $this->flagDeleted = $flagDeleted;

        return $this;
    }

    /**
     * Get flagDeleted
     *
     * @return string 
     */
    public function getFlagDeleted()
    {
        return $this->flagDeleted;
    }

}
