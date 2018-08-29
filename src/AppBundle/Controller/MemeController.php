<?php
/**
 * Created by PhpStorm.
 * User: kulin
 * Date: 26.08.2018
 * Time: 20:16
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Meme;
use AppBundle\Form\CommentType;
use AppBundle\Form\MemeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MemeController extends Controller
{
    /**
     * @Route("/{page}", name="meme_index")
     * @param int $page
     * @return Response
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function indexAction($page = 1)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $memes = $entityManager
            ->getRepository(Meme::class)
            ->findPage($page, Meme::STATUS_HOT);
        $memesCount = $entityManager
            ->getRepository(Meme::class)
            ->findMaxPages(Meme::STATUS_HOT);
        return $this
            ->render(
                "Meme/index.html.twig",
                [
                    "memes" => $memes,
                    "maxPage" => intval(ceil($memesCount / 10)),
                    "currentPage" => $page,
                    "activeMenuElement" => "hot",
                ]
            );
    }

    /**
     * @Route("/fresh", name="meme_fresh", defaults={"page" = 1})
     * @Route("/fresh/{page}", name="meme_fresh"))
     * @param int $page
     * @return Response
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function freshAction($page = 1)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $memes = $entityManager
            ->getRepository(Meme::class)
            ->findPage($page, Meme::STATUS_FRESH);
        $memesCount = $entityManager
            ->getRepository(Meme::class)
            ->findMaxPages(Meme::STATUS_FRESH);
        return $this
            ->render(
                "Meme/fresh.html.twig",
                [
                    "memes" => $memes,
                    "maxPage" => intval(ceil($memesCount / 10)),
                    "currentPage" => $page,
                    "activeMenuElement" => "fresh",
                ]
            );
    }

    /**
     * @Route("/meme/add", name="meme_add")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function addAction(Request $request)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $meme = new Meme();

        $form = $this->createForm(MemeType::class, $meme);

        if($request->isMethod("post")) {
            $form->handleRequest($request);
            if($form->isValid()) {
                $path = $meme->getImage();
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                $meme
                    ->setStatus(Meme::STATUS_FRESH)
                    ->setUser($this->getUser())
                    ->setImage($base64)
                    ->setCreatedAt(new \DateTime())
                    ->setUpVotes()
                    ->setDownVotes();

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($meme);
                $entityManager->flush();

                return $this->redirectToRoute("meme_details", ["id" => $meme->getId()]);
            }
        }
        return $this->render("Meme/add.html.twig",
            [
                "form" => $form->createView(),
                "activeMenuElement" => "add",
            ]
        );
    }

    /**
     * @Route("/meme/{id}", name="meme_details")
     * @param Meme $meme
     * @return Response
     */
    public function detailsAction(Meme $meme)
    {

        $commentForm = $this->createForm(
            CommentType::class,
            null,
            ["action" => $this->generateUrl("comment_add", ["id" => $meme->getId()])]
        );

        $upVoteForm = $this->createFormBuilder()
            ->setAction($this->generateUrl("meme_upvote", ["id" => $meme->getId()]))
            ->setMethod(Request::METHOD_POST)
            ->add("submit", SubmitType::class, ["attr" => ["class" => "btn-success btn-upvote"], "label" => "+"])
            ->getForm();

        $downVoteForm = $this->createFormBuilder()
            ->setAction($this->generateUrl("meme_downvote", ["id" => $meme->getId()]))
            ->setMethod(Request::METHOD_POST)
            ->add("submit", SubmitType::class, ["attr" => ["class" => "btn-danger btn-downvote"], "label" => "-"])
            ->getForm();

        switch ($meme->getStatus()) {
            case Meme::STATUS_HOT:
                $activeMenuElement = Meme::STATUS_HOT;
                break;
            case Meme::STATUS_FRESH:
                $activeMenuElement = Meme::STATUS_FRESH;
                break;
            default:
                $activeMenuElement = "none";
                break;
        }

        return $this->render("Meme/details.html.twig",
            [
                "meme" => $meme,
                "commentForm" =>  $commentForm->createView(),
                "upvoteForm" => $upVoteForm->createView(),
                "downvoteForm" => $downVoteForm->createView(),
                "activeMenuElement" => $activeMenuElement,
            ]
        );
    }

    /**
     * @Route ("/meme/random/", name="meme_random")
     */
    public function randomAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $query = $entityManager
            ->getRepository(Meme::class)
            ->findRandom();
        $randomMeme = $query[0];

        return $this->detailsAction($randomMeme);
    }

    /**
     * @Route("/vote/up/{id}", name="meme_upvote")
     * @param Meme $meme
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function upvoteAction(Meme $meme)
    {
        if($this->getUser() != NULL) {
            $this->denyAccessUnlessGranted("ROLE_USER");
            $entityManager = $this->getDoctrine()->getManager();
            $meme->addUpVotes();
            $entityManager->persist($meme);
            $entityManager->flush();
            return $this->redirectToRoute("meme_details", ["id" => $meme->getId()]);
        }
        else {
            $this->addFlash("error", "Aby głosować musisz się zalogować");
            return $this->redirectToRoute("meme_details", ["id" => $meme->getId()]);
        }
    }

    /**
     * @Route("/vote/down/{id}", name="meme_downvote")
     * @param Meme $meme
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function downvoteAction(Meme $meme)
    {
        if($this->getUser() != NULL) {
            $this->denyAccessUnlessGranted("ROLE_USER");
            $entityManager = $this->getDoctrine()->getManager();
            $meme->addDownVotes();
            $entityManager->persist($meme);
            $entityManager->flush();
            return $this->redirectToRoute("meme_details", ["id" => $meme->getId()]);
        }
        else {
            $this->addFlash("error", "Aby głosować musisz się zalogować");
            return $this->redirectToRoute("meme_details", ["id" => $meme->getId()]);
        }
    }
}