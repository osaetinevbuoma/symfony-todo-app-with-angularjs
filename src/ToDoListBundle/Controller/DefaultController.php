<?php

namespace ToDoListBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use ToDoListBundle\Entity\Todo;

class DefaultController extends Controller
{
	/**
	 * Display the index page
	 */
    public function indexAction()
    {
        return $this->render('ToDoListBundle:Default:index.html.twig');
    }

    /**
     * List all todos
     *
     * @return JsonResponse
     */
    public function listTodosAction()
    {
        $todos = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select("t")
            ->from("ToDoListBundle:Todo", "t")
            ->getQuery()
            ->getArrayResult();

        return new JsonResponse($todos);
    }

    /**
     * Save todo
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function saveTodoAction(Request $request)
    {
        if ($request->getMethod() == "POST") {
            $data = json_decode($request->getContent(), true);

            $todo = new Todo();
            $todo->setTitle($data["title"]);
            $todo->setDescription($data["description"]);
            $todo->setStartDate(new \DateTime($data["startDate"], new \DateTimeZone("Africa/Lagos")));
            $todo->setEndDate(new \DateTime($data["endDate"], new \DateTimeZone("Africa/Lagos")));
            $todo->setIsCompleted(false);

            $this->getDoctrine()->getManager()->persist($todo);
            $this->getDoctrine()->getManager()->flush();

            return new JsonResponse($todo->getId()); // return the id of the todo to be used
        } else
            return new JsonResponse(500);
    }

    /**
     * Edit todo
     * @param $id The id of the todo item
     * @return JsonResponse
     */
    public function editTodoAction($id)
    {
        $todo = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select("t")
            ->from("ToDoListBundle:Todo", "t")
            ->where("t.id = :id")
            ->setParameter("id", $id)
            ->getQuery()
            ->getArrayResult();

        return new JsonResponse($todo);
    }

    /**
     * Update todo item
     * @param Request $request
     * @return JsonResponse
     */
    public function updateTodoAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $todo = $this->getDoctrine()->getManager()->getRepository("ToDoListBundle:Todo")->find($data["id"]);
        $todo->setTitle($data["title"]);
        $todo->setDescription($data["description"]);
        $todo->setStartDate(new \DateTime($data["startDate"], new \DateTimeZone("Africa/Lagos")));
        $todo->setEndDate(new \DateTime($data["endDate"], new \DateTimeZone("Africa/Lagos")));
        $todo->setIsCompleted($data["isCompleted"]);

        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse(200);
    }

    /**
     * Delete todo item
     * @param $id The id of the todo item
     * @return JsonResponse
     */
    public function deleteTodoAction($id)
    {
        $todo = $this->getDoctrine()->getManager()->getRepository("ToDoListBundle:Todo")->find($id);

        $this->getDoctrine()->getManager()->remove($todo);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse(200);
    }
}
