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

    /**
     * @var string
     * @ORM\Column(name="votes", type="text", nullable=true)
     */
    private $votes = 'a:1:{i:0;a:2:{s:2:"id";i:0;s:4:"vote";s:1:"0";}}';

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

    /**
     * @param $userVotes
     * @return $this
     */
    public function setVotes($userVotes)
    {
        $this->votes = serialize($userVotes);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVotes()
    {
        return unserialize($this->votes);
    }

    /**
     * @param Meme $meme
     * @param $vote
     */
    public function addVotes(Meme $meme, $vote)
    {
        $votes = $this->getVotes();
        $votes[]["id"] = $meme->getId();
        $votes[]["vote"] = $vote;
        $this->votes = serialize($votes);
    }
}