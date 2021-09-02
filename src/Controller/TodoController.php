<?php

namespace App\Controller;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Todo;

class TodoController extends AbstractController
{
    /**
     * @Route("/", name="todo")
     */
    public function index(): Response
    {
        $todos = $this->getDoctrine()->getRepository(Todo::class)->findAll();       /** other way: (App:todo) **/ 
        // dd($todos);
        return $this->render('todo/index.html.twig', [
           'todos' => $todos 
        ]);
    }

    /**
     * @Route("/create", name="create_todo")
     */
    public function create(Request $request): Response
    {
        $todo = new Todo;
        $form = $this->createFormBuilder($todo)->add("name", TextType::class, array('attr'=>array("class"=>"form-control fw-light border-1 border-muted rounded-pill bg-light shadow-sm mt-3 text-muted", "style"=>"margin-bottom:15px")))
        ->add("category", TextType::class, array('attr'=>array("class"=>"form-control fw-light border-1 border-muted rounded-pill bg-light shadow-sm mt-3 text-muted", "style"=>"margin-bottom:15px")))
        ->add("description", TextareaType::class, array('attr'=>array("class"=>"form-control fw-light border-1 border-muted rounded bg-light shadow-sm mt-3 text-muted", "style"=>"margin-bottom:15px")))
        ->add("priority", ChoiceType::class, array('attr'=>array("class"=>"form-control fw-light border-1 border-muted rounded-pill bg-light shadow-sm mt-3 text-muted", "style"=>"margin-bottom:15px"), "choices"=> array('Low'=>'Low', 'Normal'=>'Normal', 'High'=>'High')))
        ->add("due_date", DateTimeType::class, array('attr'=>array("class"=>"form-control fw-light border-1 border-muted rounded bg-light shadow-sm mt-3 text-muted p-3", "style"=>"margin-bottom:15px")))
        ->add("save", SubmitType::class, array('attr'=>array("class"=>"btn-outline-primary fw-light btn-sm border-1 shadow-sm rounded-pill m-3", "style"=>"margin-bottom:15px"),"label"=>"create todo"))->getForm();
        // ->add('save', SubmitType::class, array('label'=> 'Create Todo', 'attr' => array('class'=> 'btn-primary', 'style'=>'margin-bottom:15px')))->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $name = $form["name"]->getData();
            $category = $form["category"]->getData();
            $description = $form["description"]->getData();
            $priority =$form["priority"]->getData();

            $due_date =$form["due_date"]->getData();

            $now = new \DateTime('now');

            $todo->setName($name);
            $todo->setCategory($category);
            $todo->setDescription($description);
            $todo->setPriority($priority);
            $todo->setDueDate($due_date);
            $todo->setCreateDue($now);

            $em = $this->getDoctrine()->getManager();

            $em->persist($todo);
            $em->flush();

            $this->addFlash('notice', 'Todo Added');

            return $this->redirectToRoute('todo');
        }

        return $this->render('todo/create.html.twig', [  
            "form" => $form->createView()
         ]);
    }

    /**
     * @Route("/details/{id}", name="details_todo")
     * 
     */
    public function details($id): Response
    {
        $todo = $this->getDoctrine()->getRepository('App:Todo')->find($id);
        return $this->render('todo/details.html.twig', [
            "todo" => $todo
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit_todo")
     */
    public function edit($id, Request $request): Response
    {
        $todo = $this->getDoctrine()->getRepository(Todo::class)->find($id);
        $form = $this->createFormBuilder($todo)
        ->add("name", TextType::class, array('attr'=>array("class"=>"form-control fw-light border-1 border-muted rounded-pill bg-light shadow-sm mt-3 text-muted", "style"=>"margin-bottom:15px")))
        ->add("category", TextType::class, array('attr'=>array("class"=>"form-control fw-light border-1 border-muted rounded-pill bg-light shadow-sm mt-3 text-muted", "style"=>"margin-bottom:15px")))
        ->add("description", TextareaType::class, array('attr'=>array("class"=>"form-control fw-light border-1 border-muted rounded bg-light shadow-sm mt-3 text-muted", "style"=>"margin-bottom:15px")))
        ->add("priority", ChoiceType::class, array('attr'=>array("class"=>"form-control fw-light border-1 border-muted rounded-pill bg-light shadow-sm mt-3 text-muted", "style"=>"margin-bottom:15px"), "choices"=> array('Low'=>'Low', 'Normal'=>'Normal', 'High'=>'High')))
        ->add("due_date", DateTimeType::class, array('attr'=>array("class"=>"form-control fw-light border-1 border-muted rounded bg-light shadow-sm mt-3 text-muted p-3", "style"=>"margin-bottom:15px ")))
        ->add("save", SubmitType::class, array('attr'=>array("class"=>"btn-outline-primary fw-light btn-sm border-1 shadow-sm rounded-pill m-3", "style"=>"margin-bottom:15px; "),"label"=>"edit todo"))->getForm();
        // ->add('save', SubmitType::class, array('label'=> 'Create Todo', 'attr' => array('class'=> 'btn-primary', 'style'=>'margin-bottom:15px')))->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $name = $form["name"]->getData();
            $category = $form["category"]->getData();
            $description = $form["description"]->getData();
            $priority =$form["priority"]->getData();

            $due_date =$form["due_date"]->getData();

            $now = new \DateTime('now');

            $todo->setName($name);
            $todo->setCategory($category);
            $todo->setDescription($description);
            $todo->setPriority($priority);
            $todo->setDueDate($due_date);
            $todo->setCreateDue($now);

            $em = $this->getDoctrine()->getManager();

            $em->persist($todo);
            $em->flush();

            $this->addFlash('notice', 'Todo Edited');

            return $this->redirectToRoute('todo');
        }
        
        return $this->render('todo/edit.html.twig', [  
            "form" => $form->createView()
         ]);
    }

    /**
     * @Route("/delete/{id}", name="delete_todo")
     * 
     */
    public function delete($id): Response
    {   $em = $this->getDoctrine()->getManager();
        $todo = $em->getRepository('App:Todo')->find($id);
        // dd($todo);
        $em->remove($todo);
        $em->flush();

        $this->addFlash('notice', 'Todo Removed');

        return $this->redirectToRoute('todo');
    }

    
}
