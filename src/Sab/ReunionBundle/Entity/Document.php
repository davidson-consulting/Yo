<?php

namespace Sab\ReunionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entité document
 *
 * @ORM\Table(name="document")
 * @ORM\Entity
 */
class Document
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="pathSource", type="string", length=255)
     */
    private $pathSource;
    
    
    /**
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    
    
    /**
     *
     * @ORM\ManyToOne(targetEntity="Sab\ReunionBundle\Entity\Event")
     */
    private $event;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;


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
     * Set pathSource
     *
     * @param string $pathSource
     * @return Document
     */
    public function setPathSource($pathSource)
    {
        $this->pathSource = $pathSource;

        return $this;
    }

    /**
     * Get pathSource
     *
     * @return string 
     */
    public function getPathSource()
    {
        return $this->pathSource;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Document
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set event
     *
     * @param \Sab\ReunionBundle\Entity\Event $event
     * @return Document
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
     * Set name
     *
     * @param string $name
     * @return Document
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
}
