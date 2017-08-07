<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ArticleController extends Controller
{
    /**
     * @Route("/taxes", name="taxes")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \LogicException
     */
    public function applicationAction(Request $request)
    {
        $article = new Article();

        $form = $this->createFormBuilder()
            ->add('nom', TextType::class)
            ->add('description', TextareaType::class, array(
                'required' => false
            ))
            ->add('montant_ht', NumberType::class, array(
                'scale' => 2,
                'attr' => array(
                    'min' => 0
                )
            ))
            ->add('valider', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $prixHT = $data['montant_ht'];

            $article->setAmountHT($prixHT);
            $article->setNom($data['nom']);
            $article->setDescription($data['description']);
            $article->setCreation(new \DateTime('now'));

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            return $this->redirect($request->getUri());
        }

        $articles = $this->getArticles();

        $copiedArt = array();

        foreach ($articles as $element) {
            $copiedArt[] = array(
                'nom' => $element->getNom(),
                'description' => $element->getDescription(),
                'amountHT' => $element->getAmountHT(),
                'creation' => $element->getCreation(),
                'amountTTC1' => $this->calcTVA1($element->getAmountHT()),
                'amountTTC2' => $this->calcTVA2($element->getAmountHT())
            );
        }

        return $this->render(
            'default/taxes.html.twig',
            array('form' => $form->createView(),
                'copiedArt' => $copiedArt)
        );
    }

    /**
     * @param float $number
     * @return float
     */
    public function calcTVA1(float $number): float
    {
        return $number * 1.17;
    }

    /**
     * @param float|int $number
     * @return float
     */
    public function calcTVA2(float $number): float
    {
        return $number * 1.03;
    }

    /**
     * @return array
     * @throws \LogicException
     */
    public function getArticles(): array
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Article');
        $articles = $repository->findAll();

        return $articles;
    }
}