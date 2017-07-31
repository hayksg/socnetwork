<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;

/**
 * Status
 *
 * @ORM\Table(name="status", indexes={@ORM\Index(name="user_id_key", columns={"user_id"}), @ORM\Index(name="parent_id_key", columns={"parent_id"})})
 * @ORM\Entity(repositoryClass="Application\Entity\Repository\StatusRepository")
 *
 * @Annotation\Name("status")
 */
class Status
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Annotation\Exclude()
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="parent_id", type="integer", precision=0, scale=0, nullable=true, unique=false)
     *
     * @Annotation\Exclude()
     */
    private $parentId;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", length=65535, precision=0, scale=0, nullable=false, unique=false)
     *
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Attributes({
     *     "class":"form-control",
     *     "id":"content",
     *     "required":"required",
     *     "name":"content",
     *     "rows":"3"
     * })
     * @Annotation\Required({"required":"true"})
     * @Annotation\Options({"min":"2", "max":"1000"})
     * @Annotation\Filter({"name":"stripTags"})
     * @Annotation\Filter({"name":"stringTrim"})
     * @Annotation\Validator({"name":"stringLength", "options":{
     *     "encoding":"utf-8",
     *     "min":"2",
     *     "max":"1000"
     * }})
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", precision=0, scale=0, nullable=true, unique=false)
     */
    private $createdAt;

    /**
     * @var \Application\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $user;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"class":"btn btn-default", "value":"Update status"})
     * @Annotation\AllowEmpty({"allowempty":"true"})
     */
    private $submit;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
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
     * Set parentId
     *
     * @param integer $parentId
     *
     * @return Status
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * Get parentId
     *
     * @return integer
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Status
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Status
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set user
     *
     * @param \Application\Entity\User $user
     *
     * @return Status
     */
    public function setUser(\Application\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Application\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}

