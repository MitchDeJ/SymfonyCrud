<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class PostController extends AbstractController
{

    private $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * @Route("/post", name="post")
     */
    public function index()
    {
        $repository = $this->getDoctrine()
            ->getRepository(Post::class);
        $posts = $repository->findAll();

        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/post/add", name="post_add", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        $title = $data['title'];
        $content = $data['content'];
        $author = $data['author'];

        if (empty($title) || empty($content) || empty($author)) {
            throw new NotFoundHttpException('Missing parameters!');
        }

        $post = new Post();

        $post
            ->setTitle($title)
            ->setContent($content)
            ->setAuthor($author);
        $em = $this->getDoctrine()->getManager();
        $em->persist($post);
        $em->flush();
        return new JsonResponse(['status' => 'Post created!'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/post/get/{id}", name="post_get", methods={"GET"})
     */
    public function getOne($id): JsonResponse
    {
        $post = $this->postRepository->findOneBy(['id' => $id]);

        $data = [
            'id' => $post->getId(),
            'title' => $post->getTitle(),
            'content' => $post->getContent(),
            'author' => $post->getAuthor()
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/post/getall", name="post_get_all", methods={"GET"})
     */
    public function getAll(): JsonResponse
    {
        $posts = $this->postRepository->findAll();
        $data = [];

        foreach ($posts as $post) {
            $data[] = [
                'id' => $post->getId(),
                'title' => $post->getTitle(),
                'content' => $post->getContent(),
                'author' => $post->getAuthor(),
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/post/update/{id}", name="post_update", methods={"PUT"})
     */
    public function update($id, Request $request): JsonResponse
    {
        $post = $this->postRepository->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true);

        empty($data['title']) ? true : $post->setTitle($data['title']);
        empty($data['content']) ? true : $post->setContent($data['content']);
        empty($data['author']) ? true : $post->setAuthor($data['author']);

        $em = $this->getDoctrine()->getManager();
        $em->persist($post);
        $em->flush();

        return new JsonResponse($post->toArray(), Response::HTTP_OK);
    }

    /**
     * @Route("/post/delete/{id}", name="post_delete", methods={"DELETE"})
     */
    public function delete($id): JsonResponse
    {
        $post = $this->postRepository->findOneBy(['id' => $id]);

        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();

        return new JsonResponse(['status' => 'Post #'.$id.' deleted.'], Response::HTTP_OK);
    }


}
