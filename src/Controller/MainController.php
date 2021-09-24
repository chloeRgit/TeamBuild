<?php

namespace App\Controller;
use App\Entity\Equipe;
use App\Entity\Personne;
use App\Form\EquipeType;
use App\Form\PersonneType;
use App\Repository\EquipeRepository;
use App\Repository\PersonneRepository;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function index(EquipeRepository $repoEquipe,Request $request, PersonneRepository $repoPersonne): Response
    {
        $equipe= new Equipe();
        $formEquipe=$this->createForm(EquipeType::class, $equipe);
        $formEquipe->handleRequest($request);

        $personne = new Personne();
        $formPersonne = $this->createForm(PersonneType::class, $personne);
        $formPersonne->handleRequest($request);

      if($formEquipe->isSubmitted()){
            $em=$this->getDoctrine()->getManager();
            $em->persist($equipe);
            $em->flush();
           // $equipes=$repoEquipe->findAll();
            return $this->render('main/index.html.twig', [
                'equipes'=>$repoEquipe->findAll(),
                'personnes'=>$repoPersonne->findAll(),
                'formEquipe' => $formEquipe->createView(),
                'formPersonne' => $formPersonne->createView(),

                //     'success_ajout'=>'success'
            ]);
        }
        if($formPersonne->isSubmitted()){
            $em=$this->getDoctrine()->getManager();
            $equipec = $repoEquipe->find($_REQUEST['personne']['equipes']);
            $personne->addEquipe($equipec);
            $em->persist($personne);
            $em->flush();
            //$personnes=$repoEquipe->findAll();
            return $this->render('main/index.html.twig', [
                'equipes'=>$repoEquipe->findAll(),
                'personnes'=>$repoPersonne->findAll(),
                'formPersonne' => $formPersonne->createView(),
                'formEquipe' => $formEquipe->createView(),
                //     'success_ajout'=>'success'
            ]);
        }

        return $this->render('main/index.html.twig', [
            'formEquipe' => $formEquipe->createView(),
            'equipes'=>$repoEquipe->findAll(),
            'formPersonne' => $formPersonne->createView(),
            'personnes'=>$repoPersonne->findAll(),
        ]);

    }

    /**
     * @Route("/equipe_delete/{id}", name="equipe_delete")
     */
    public function equipedelete(Equipe $equipe): Response
    {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($equipe);
            $entityManager->flush();

        return $this->redirectToRoute('main');
    }

    /**
     * @Route("/personne_delete/{id}", name="personne_delete")
     */
    public function personnedelete(Personne $personne): Response
    {

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($personne);
        $entityManager->flush();

        return $this->redirectToRoute('main');
    }


    /**
     * @Route("/personne_remove/{idp,ide}", name="personne_remove")
     */
    public function personneremove(EquipeRepository $repoEquipe,PersonneRepository $repoPersonne,Request $request): Response
    {
        //$em=$this->getDoctrine()->getManager();
        $equipec = $repoEquipe->find($_REQUEST['ide']);
        $personnec = $repoPersonne->find($_REQUEST['idp']);
        $personnec->removeEquipe($equipec);
        $entityManager = $this->getDoctrine()->getManager();
        //$entityManager->remove($personnec);
        $entityManager->flush();

        return $this->redirectToRoute('main');
    }
}
