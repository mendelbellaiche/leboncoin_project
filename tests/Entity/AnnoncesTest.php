<?php

namespace App\Tests\Entity;

use App\Entity\Annonce;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class AnnoncesTest extends WebTestCase {

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


    public function testAlgorithmMarqueAndModel()
    {
        $values = $this->scanMarqueAndModel("rs4 avant");
        $this->assertEquals("Audi", $values[0]);
        $this->assertEquals("Rs4", $values[1]);

        $values = $this->scanMarqueAndModel("Gran Turismo Série5");
        $this->assertEquals("BMW", $values[0]);
        $this->assertEquals("Serie 5", $values[1]);

        $values = $this->scanMarqueAndModel("ds 3 crossback");
        $this->assertEquals("Citroen", $values[0]);
        $this->assertEquals("Ds3", $values[1]);

        $values = $this->scanMarqueAndModel("CrossBack ds 3");
        $this->assertEquals("Citroen", $values[0]);
        $this->assertEquals("Ds3", $values[1]);

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
