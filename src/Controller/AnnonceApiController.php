<?php

namespace App\Controller;

use App\Entity\Annonce;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class AnnonceApiController extends AbstractController
{
    private $marques = array(
        "Audi" => array("Cabriolet", "Q2", "Q3", "Q5", "Q7", "Q8", "R8",
            "Rs3", "Rs4", "Rs5", "Rs7", "S3", "S4", "S4 Avant",  "S4 Cabriolet",
            "S5", "S7", "S8", "SQ5", "SQ7", "Tt", "Tts", "V8"),
        "BMW" => array( "M3", "M4", "M5", "M535", "M6", "M635", "Serie 1", "Serie 2",
            "Serie 3", "Serie 4", "Serie 5", "Serie 6", "Serie 7", "Serie 8"),
        "Citroen" => array("C1", "C15", "C2", "C25", "C25D", "C25E", "C25TD", "C3",
            "C3 Aircross", "C3 Picasso", "C4", "C4 Picasso", "C5", "C6", "C8", "Ds3",
            "Ds4", "Ds5")
    );

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    #[Route('/api', name: 'app_annonce_api_read_all', methods: ["GET"])]
    public function index(): Response
    {
        $annonces = $this->entityManager->getRepository(Annonce::class)->findAll();
        return $this->json(array('code' => 200, "annonces" => $annonces));
    }

    #[Route('/api/{id}', name: 'app_annonce_api_read_one', methods: ["GET"])]
    public function readOne($id): Response
    {
        $annonce = $this->entityManager->getRepository(Annonce::class)->findOneById($id);
        if (is_null($annonce)) {
            throw new NotFoundResourceException("Resource not found!");
        }
        return $this->json(array('code' => 200, "annonce" => $annonce));
    }

    #[Route('/api', name: 'app_annonce_api_create', methods: ["POST"])]
    public function createAnnonce(Request $request): Response
    {

        if (is_null($request->request->get("title"))
            || is_null($request->request->get("content"))
            || is_null($request->request->get("category")) )
        {
            throw new NotFoundResourceException("Resource not found!");
        }

        $title = $request->request->get("title");
        $content = $request->request->get("content");
        $category = $request->request->get("category");
        $model = null;

        if ($category != "Emploi"
            && $category != "Automobile"
            && $category != "Immobilier")
        {
            throw new \InvalidArgumentException("Invalid Argument!");
        }

        if (
            $category == "Automobile"
            && is_null($request->request->get("model"))
        ) {
            throw new NotFoundResourceException("Resource not found!");
        } else if (
            $category == "Automobile"
            && !is_null($request->request->get("model"))
        )  {
            $model = $request->request->get("model");
        }

        $marqueAndModel = $this->scanMarqueAndModel($model);
        $marqueToDB = $marqueAndModel[0];
        $modelToDB = $marqueAndModel[1];

        $annonce = new Annonce();
        $annonce->setTitle($title);
        $annonce->setContent($content);
        $annonce->setCategory($category);
        $annonce->setMarque($marqueToDB);
        $annonce->setModel($modelToDB);

        $this->entityManager->persist($annonce);
        $this->entityManager->flush();

        return $this->json(array('code' => 200, 'id' => $annonce->getId()));

    }

    #[Route('/api/{id}', name: 'app_annonce_api_update', methods: ["PUT"])]
    public function updateAnnonce(Request $request, $id): Response
    {
        if (is_null($request->request->get("title"))
            || is_null($request->request->get("content"))
            || is_null($request->request->get("category")) )
        {
            throw new NotFoundResourceException("Resource not found!");
        }

        $title = $request->request->get("title");
        $content = $request->request->get("content");
        $category = $request->request->get("category");
        $model = null;

        if ($category != "Emploi"
            && $category != "Automobile"
            && $category != "Immobilier")
        {
            throw new \InvalidArgumentException("Invalid Argument!");
        }

        if (
            $category == "Automobile"
            && is_null($request->request->get("model"))
        ) {
            throw new NotFoundResourceException("Resource not found!");
        } else if (
            $category == "Automobile"
            && !is_null($request->request->get("model"))
        )  {
            $model = $request->request->get("model");
        }

        $marqueAndModel = $this->scanMarqueAndModel($model);
        $marqueToDB = $marqueAndModel[0];
        $modelToDB = $marqueAndModel[1];

        $annonce = $this->entityManager->getRepository(Annonce::class)->findOneById($id);

        if (is_null($annonce)) {
            throw new NotFoundResourceException("Resource not found!");
        }

        $annonce->setTitle($title);
        $annonce->setContent($content);
        $annonce->setCategory($category);
        $annonce->setMarque($marqueToDB);
        $annonce->setModel($modelToDB);

        $this->entityManager->persist($annonce);
        $this->entityManager->flush();

        return $this->json(array('code' => 200, 'id' => $annonce->getId()));
    }

    #[Route('/api/{id}', name: 'app_annonce_api_delete', methods: ["DELETE"])]
    public function deleteAnnonce($id): Response
    {

        $annonce = $this->entityManager->getRepository(Annonce::class)->findOneById($id);

        if (is_null($annonce)) {
            throw new NotFoundResourceException("Resource not found!");
        }

        $this->entityManager->remove($annonce);
        $this->entityManager->flush();

        return $this->json(array('code' => 200));

    }

    private function scanMarqueAndModel($model) {

        $model = strtolower($model);
        $model = str_replace("série", "serie", $model);

        $model = str_replace("q 1", "q1", $model);
        $model = str_replace("q 3", "q3", $model);
        $model = str_replace("q 5", "q5", $model);
        $model = str_replace("q 7", "q7", $model);
        $model = str_replace("q 8", "q8", $model);
        $model = str_replace("r 8", "r8", $model);
        $model = str_replace("rs 3", "rs3", $model);
        $model = str_replace("rs 4", "rs4", $model);
        $model = str_replace("rs 5", "rs5", $model);
        $model = str_replace("rs 7", "rs7", $model);
        $model = str_replace("s 3", "s3", $model);
        $model = str_replace("s 4", "s4", $model);
        $model = str_replace("s4avant", "S4 avant", $model);
        $model = str_replace("s4cabriolet", "S4 cabriolet", $model);
        $model = str_replace("s 5", "s5", $model);
        $model = str_replace("s 7", "s7", $model);
        $model = str_replace("s 8", "s8", $model);
        $model = str_replace("sq 5", "sq5", $model);
        $model = str_replace("sq 7", "sq7", $model);
        $model = str_replace("v 8", "v8", $model);

        $model = str_replace("m 3", "m3", $model);
        $model = str_replace("m 4", "m4", $model);
        $model = str_replace("m 5", "m5", $model);
        $model = str_replace("m 535", "m535", $model);
        $model = str_replace("m 6", "m6", $model);
        $model = str_replace("m 635", "m635", $model);
        $model = str_replace("serie1", "serie 1", $model);
        $model = str_replace("serie2", "serie 2", $model);
        $model = str_replace("serie3", "serie 3", $model);
        $model = str_replace("serie4", "serie 4", $model);
        $model = str_replace("serie5", "serie 5", $model);
        $model = str_replace("serie6", "serie 6", $model);
        $model = str_replace("serie7", "serie 7", $model);
        $model = str_replace("serie8", "serie 8", $model);

        $model = str_replace("c 1", "c1", $model);
        $model = str_replace("c 15", "c15", $model);
        $model = str_replace("c 2", "c2", $model);
        $model = str_replace("c 25", "c25", $model);
        $model = str_replace("c 25d", "c25d", $model);
        $model = str_replace("c 25 d", "c25d", $model);
        $model = str_replace("c 25e", "c25e", $model);
        $model = str_replace("c 25 e", "c25e", $model);
        $model = str_replace("c 25 td", "c25td", $model);
        $model = str_replace("c 3", "c3", $model);
        $model = str_replace("c3aircross", "c3 aircross", $model);
        $model = str_replace("c3picasso", "c3 picasso", $model);
        $model = str_replace("c 4", "c4", $model);
        $model = str_replace("c4picasso", "c4 picasso", $model);
        $model = str_replace("c 5", "c5", $model);
        $model = str_replace("c 6", "c6", $model);
        $model = str_replace("c 8", "c8", $model);
        $model = str_replace("ds 3", "ds3", $model);
        $model = str_replace("ds 4", "ds4", $model);
        $model = str_replace("ds 5", "ds5", $model);

        $marqueToDB = null;
        $modelToDB = null;

        foreach ($this->marques as $marqueArray => $modelsOfMarque) {
            foreach ($modelsOfMarque as $modelOfMarque) {

                # pour 'rs4 avant' => 'rs4' et non 's4 avant'
                if (str_contains(strtolower($model), "rs4")
                    && str_starts_with(strtolower($modelOfMarque), "s4")) {
                    continue;
                }

                if (str_contains(strtolower($model), strtolower($modelOfMarque))) {
                    $marqueToDB = $marqueArray;
                    $modelToDB = $modelOfMarque;
                }

            }
        }

        return array($marqueToDB, $modelToDB);
    }
}
