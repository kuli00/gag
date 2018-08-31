<?php
/**
 * Created by PhpStorm.
 * User: kulin
 * Date: 27.08.2018
 * Time: 21:03
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Meme;
use AppBundle\Entity\User;
use AppBundle\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends Controller
{
    /**
     * @return $this
     */
    public function index()
    {
        return $this;
    }

    /**
     * @Route("/comment/add/{id}", name="comment_add", methods={"POST"})
     * @param Request $request
     * @param Meme $meme
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addAction(Request $request, Meme $meme)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);

        $commentForm->handleRequest($request);

        if ($commentForm->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $comment
                ->setCreatedAt(new \DateTime())
                ->setMeme($meme)
                ->setUser($this->getUser());

            $entityManager->persist($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute("meme_details", ["id" => $meme->getId()]);
    }
}