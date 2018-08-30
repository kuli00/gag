<?php

namespace AppBundle\Entity;

use Doctrine\DBAL\Types\BlobType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Meme
 *
 * @ORM\Table(name="meme")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MemeRepository")
 */
class Meme
{
    const STATUS_HOT = "hot";
    const STATUS_FRESH = "fresh";
    const STATUS_ARCHIVE = "archive";

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(name="image", type="text")
     */
    private $image;

    /**
     * @var \DateTime
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var string
     * @ORM\Column(name="status", type="string")
     */
    private $status;

    /**
     * @var Comment[]
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="meme")
     */
    private $comments;


    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="memes")
     */
    private $user;

    /**
     * @var int
     * @ORM\Column(name="upVotes", type="integer")
     */
    private $upVotes;

    /**
     * @var int
     * @ORM\Column(name="downVotes", type="integer")
     */
    private $downVotes;

    /**
     * @var int
     * @ORM\Column(name="votesRate", type="integer")
     */
    private $votesRate;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Meme
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param $image
     * @return $this
     */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param \DateTime $dateTime
     * @return $this
     */
    public function setCreatedAt(\DateTime $dateTime)
    {
        $this->createdAt = $dateTime;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getcreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set comment
     *
     * @param array $comment
     *
     * @return Meme
     */
    public function addComment(Comment $comment)
    {
        $this->comments = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return array
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return $this
     */
    public function setUpVotes()
    {
        $this->upVotes = 0;
        return $this;
    }

    /**
     * @return int
     */
    public function getUpVotes()
    {
        return $this->upVotes;
    }

    /**
     * @return int
     */
    public function addUpVotes()
    {
        $this->upVotes += 1;
        return $this->upVotes;
    }

    /**
     * @return $this
     */
    public function setDownVotes()
    {
        $this->downVotes = 0;
        return $this;
    }

    /**
     * @return int
     */
    public function getDownVotes()
    {
        return $this->downVotes;
    }

    /**
     * @return int
     */
    public function addDownVotes()
    {
        $this->downVotes += 1;
        return $this->downVotes;
    }

    /**
     * @param $votesRate
     * @return $this
     */
    public function setVotesRate($votesRate)
    {
        $this->votesRate = $votesRate;
        return $this;
    }

    /**
     * @return int
     */
    public function getVotesRate()
    {
        return $this->votesRate;
    }

    /**
     * @return $this
     */
    public function updateVotesRate()
    {
        $votesRate = $this->getUpVotes() - $this->getDownVotes();
        $this->setVotesRate($votesRate);
        return $this;
    }
}

