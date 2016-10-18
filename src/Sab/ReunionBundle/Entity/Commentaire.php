<?php
namespace Sab\ReunionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *Commentaire
 *
 *@ORM\Table()
 *@ORM\Entity(repositoryClass="Sab\ReunionBundle\Repository\CommentaireRepository")
 *
 */

class Commentaire
{
	/**
	 *@var integer
	 *
	 *@ORM\Column(name="id", type="integer")
	 *@ORM\Id
	 *@ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 *
	 *@ORM\ManyToOne(targetEntity="Sab\ReunionBundle\Entity\Question", inversedBy="commentaire")
	 *@ORM\JoinColumn(name="question_id", referencedColumnName="id")
	 *
	 */
	protected $question;

	/**
	 *
	 *@var string
	 *
	 *@ORM\Column(name="texte", type="string", length=255, nullable=true)
	 */
	protected $texte;


	/**
	 *@var string
	 *
	 *@ORM\Column(name="auteur", type="string", length=255, nullable=true)
	 */
	protected $auteur;


	/**
	 *@var string
	 *@ORM\Column(name="datePublication", type="datetime")
	 *
	 *
	 */
	protected $datePublication;

    /**
     *@var string
     *@ORM\Column(name="flagDeleted", type="boolean", options={"default":false})
     *
     *
     */
    protected $flagDeleted;

    /**
     *@var integer
     *@ORM\Column(name="level", type="integer",options={"default":1})
     *
     *
     */
    protected $level;


    /**
     *@var integer
     *@ORM\Column(name="parentCommentId", type="integer" , options={"default":0})
     *
     *
     */
    protected $parentCommentId;


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
     * Set texte
     *
     * @param string $texte
     * @return Commentaire
     */
    public function setTexte($texte)
    {
        $this->texte = $texte;

        return $this;
    }

    /**
     * Get texte
     *
     * @return string 
     */
    public function getTexte()
    {
        return $this->texte;
    }

    /**
     * Set auteur
     *
     * @param string $auteur
     * @return Commentaire
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
     * Set datePublication
     *
     * @param \DateTime $datePublication
     * @return Commentaire
     */
    public function setDatePublication($datePublication)
    {
        $this->datePublication = new \DateTime();

        return $this;
    }

    /**
     * Get datePublication
     *
     * @return \DateTime 
     */
    public function getDatePublication()
    {
        return $this->datePublication;
    }

    /**
     * Set question
     *
     * @param \Sab\ReunionBundle\Entity\Question $question
     * @return Commentaire
     */
    public function setQuestion(\Sab\ReunionBundle\Entity\Question $question = null)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return \Sab\ReunionBundle\Entity\Question 
     */
    public function getQuestion()
    {
        return $this->question;
    }

    public function __construct()
    {
        $this->datePublication = new \DateTime();
    }
    
    /**
     * Set flagDeleted
     *
     * @param string $flagDeleted
     * @return Commentaire
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


    /**
     * Set level
     *
     * @param integer $integer
     * @return Commentaire
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return integer 
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set parentCommentId
     *
     * @param integer $integer
     * @return Commentaire
     */
    public function setParentCommentId($parentCommentId)
    {
        $this->parentCommentId = $parentCommentId;

        return $this;
    }

    /**
     * Get parentCommentId
     *
     * @return integer 
     */
    public function getParentCommentId()
    {
        return $this->parentCommentId;
    }

}
