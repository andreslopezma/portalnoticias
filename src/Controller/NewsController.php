<?php

namespace App\Controller;

use App\Entity\Authors;
use App\Entity\Category;
use App\Entity\News;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/news")
 */
class NewsController extends AbstractController
{
    /**
     * Page index the news
     * @Route("/", name="news")
     */
    public function indexAction(ManagerRegistry $doctrine): Response
    {
        return $this->render('news/index.html.twig', [
            'categorys' => call_user_func(function () use ($doctrine) {
                $categorys = new ArrayCollection($doctrine->getRepository(Category::class)->findAll());
                return $categorys->filter(function ($category) {
                    return $category->getActive();
                });
            }),
        ]);
    }

    /**
     * Template usuado para la creacion de las noticias
     * @Route("/template/create", name="news_template_create")
     */
    public function newsAction(ManagerRegistry $doctrine): Response
    {
        return $this->render('news/create.html.twig', [
            'categorys' => call_user_func(function () use ($doctrine) {
                $categorys = new ArrayCollection($doctrine->getRepository(Category::class)->findAll());
                return $categorys->filter(function ($category) {
                    return $category->getActive();
                });
            }),
            'authors' => call_user_func(function () use ($doctrine) {
                $authors = new ArrayCollection($doctrine->getRepository(Authors::class)->findAll());
                return $authors->filter(function ($author) {
                    return $author->getActive();
                });
            })
        ]);
    }

    /**
     * Template usuado para cargar las noticas de deportes
     * @Route("/deportes", name="news_template_deports")
     */
    public function getDeporteAction(ManagerRegistry $doctrine): Response
    {
        return $this->render('news/deportes.html.twig', [
            'news' => call_user_func(function () use ($doctrine) {
                $news = new ArrayCollection($doctrine->getRepository(News::class)->findBy(['category' => Category::DEPORTES]));
                return $news->filter(function ($new) {
                    return $new->getActive();
                });
            })
        ]);
    }

    /**
     * Template usuado para cargar las noticas relacionadas con politica
     * @Route("/politica", name="news_template_politica")
     */
    public function getPoliticaAction(ManagerRegistry $doctrine): Response
    {
        return $this->render('news/politica.html.twig', [
            'news' => call_user_func(function () use ($doctrine) {
                $news = new ArrayCollection($doctrine->getRepository(News::class)->findBy(['category' => Category::POLITICA]));
                return $news->filter(function ($new) {
                    return $new->getActive();
                });
            })
        ]);
    }

    /**
     * Template usuado para cargar las noticas internacionales
     * @Route("/internacional", name="news_template_internacional")
     */
    public function getInternacionalAction(ManagerRegistry $doctrine): Response
    {
        return $this->render('news/internacional.html.twig', [
            'news' => call_user_func(function () use ($doctrine) {
                $news = new ArrayCollection($doctrine->getRepository(News::class)->findBy(['category' => Category::INTERNACIONAL]));
                return $news->filter(function ($new) {
                    return $new->getActive();
                });
            })
        ]);
    }

    /**
     * Template usuado para cargar las noticas internacionales
     * @Route("/tecnologia", name="news_template_tecnologia")
     */
    public function getTecnologiaAction(ManagerRegistry $doctrine): Response
    {
        return $this->render('news/tecnologia.html.twig', [
            'news' => call_user_func(function () use ($doctrine) {
                $news = new ArrayCollection($doctrine->getRepository(News::class)->findBy(['category' => Category::TECNOLGIA]));
                return $news->filter(function ($new) {
                    return $new->getActive();
                });
            })
        ]);
    }

    /**
     * Template encargado para mostrar la configuracion de los post
     * @Route("/admin", name="news_template_admin")
     */
    public function getAdminPostAction(ManagerRegistry $doctrine): Response
    {
        return $this->render('news/admin.html.twig', [
            'news' => call_user_func(function () use ($doctrine) {
                $news = new ArrayCollection($doctrine->getRepository(News::class)->findAll());
                return $news->filter(function ($new) {
                    return $new->getActive();
                });
            })
        ]);
    }

    /**
     * Template encargado para mostrar la configuracion de los post
     * @Route("/form/edit/{id}", name="news_template_edit_post")
     */
    public function getEditPostAction(ManagerRegistry $doctrine, $id): Response
    {
        if (!$new = $doctrine->getRepository(News::class)->find($id)) {
            throw new \Exception("El post {$id} no existe!");
        }
        return $this->render('news/editPost.html.twig', [
            'new' => $new,
            'categorys' => call_user_func(function () use ($doctrine) {
                $categorys = new ArrayCollection($doctrine->getRepository(Category::class)->findAll());
                return $categorys->filter(function ($category) {
                    return $category->getActive();
                });
            }),
            'authors' => call_user_func(function () use ($doctrine) {
                $authors = new ArrayCollection($doctrine->getRepository(Authors::class)->findAll());
                return $authors->filter(function ($author) {
                    return $author->getActive();
                });
            })
        ]);
    }

    /**
     * Guarda de manera logica las noticias
     * @Route("/create", name="news_create", methods={"POST"})
     */
    public function createNewsAction(
        ManagerRegistry $doctrine,
        Request $request,
        ValidatorInterface $validator
    ): JsonResponse {
        $r = [];
        $em = $doctrine->getManager();
        $em->beginTransaction();
        try {
            // get info by form
            [
                'categoria' => $categoria,
                'comentario' => $comentario,
                'title' => $titulo,
                'autor' => $autor
            ] = $request->get('data');
            // validate entity category
            if (!$category = $doctrine->getRepository(Category::class)->find($categoria)) {
                throw new \Exception("La categoria {$categoria} no existe!");
            }
            // validate entity Author
            if (!$author = $doctrine->getRepository(Authors::class)->find($autor)) {
                throw new \Exception("El autor {$autor} no existe!");
            }
            $news = new News();
            $news->setCategory($category);
            $news->setAuthor($author);
            $news->setTitle($titulo);
            $news->setContent($comentario);
            $em->persist($news);
            $errors = $validator->validate($news);
            if (count($errors) > 0) {
                throw new \Exception((string) $errors);
            }
            $em->flush();
            $em->commit();
            $r = ['process' => true];
        } catch (\Exception $e) {
            $em->rollback();
            $r = [
                'process' => false,
                'error' => $e->getMessage()
            ];
        }
        return new JsonResponse($r);
    }

    /**
     * Editar de manera logica los post
     * @Route("/edit/{id}", name="news_edit", methods={"POST"})
     */
    public function editNewsAction(
        $id,
        ManagerRegistry $doctrine,
        Request $request,
        ValidatorInterface $validator
    ): JsonResponse {
        $r = [];
        $em = $doctrine->getManager();
        $em->beginTransaction();
        try {
            if (!$new = $doctrine->getRepository(News::class)->find($id)) {
                throw new \Exception("El post {$id} no existe!");
            }
            // get info by form
            [
                'categoria' => $categoria,
                'comentario' => $comentario,
                'title' => $titulo,
                'autor' => $autor
            ] = $request->get('data');
            // validate entity category
            if (!$category = $doctrine->getRepository(Category::class)->find($categoria)) {
                throw new \Exception("La categoria {$categoria} no existe!");
            }
            // validate entity Author
            if (!$author = $doctrine->getRepository(Authors::class)->find($autor)) {
                throw new \Exception("El autor {$autor} no existe!");
            }
            $new->setCategory($category);
            $new->setAuthor($author);
            $new->setTitle($titulo);
            $new->setContent($comentario);
            $errors = $validator->validate($new);
            if (count($errors) > 0) {
                throw new \Exception((string) $errors);
            }
            $em->flush();
            $em->commit();
            $r = ['process' => true];
        } catch (\Exception $e) {
            $em->rollback();
            $r = [
                'process' => false,
                'error' => $e->getMessage()
            ];
        }
        return new JsonResponse($r);
    }

    /**
     * Obtiene todas las noticias activas
     * @Route("/all/news", name="news_all_news")
     */
    public function allNewsAction(ManagerRegistry $doctrine): JsonResponse
    {
        $r = [];
        $em = $doctrine->getManager();
        try {
            $news = new ArrayCollection($em->getRepository(News::class)->findAll());
            $r = [
                'news' => $news->filter(function ($entity) {
                    return $entity->getActive();
                })->map(function ($entity) {
                    return [
                        'id' => $entity->getId(),
                        'titulo' => $entity->getTitle(),
                        'contenido' => $entity->getContent(),
                        'fechaCreacion' => $entity->getDateCreation(),
                        'autor' => $entity->getAuthor()->getName(),
                        'categoria' => $entity->getCategory()->getName()
                    ];
                })->getValues()
            ];
        } catch (\Exception $e) {
            $r = [
                'process' => false,
                'error' => $e->getMessage()
            ];
        }
        return new JsonResponse($r);
    }
}
