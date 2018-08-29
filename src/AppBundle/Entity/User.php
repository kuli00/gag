<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Meme[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="Meme", mappedBy="user")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $memes;

    /**
     * @var Comment[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="user")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $comments;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return Meme[]|ArrayCollection
     */
    public function getMemes()
    {
        return $this->memes;
    }

    /**
     * @param Meme $meme
     * @return $this
     */
    public function addMeme(Meme $meme)
    {
        $this->memes[] = $meme;
        return $this;
    }

    /**
     * @return Comment[]|ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param Comment $comment
     * @return $this
     */
    public function addComment(Comment $comment)
    {
        $this->comments[] = $comment;
        return $this;
    }
}