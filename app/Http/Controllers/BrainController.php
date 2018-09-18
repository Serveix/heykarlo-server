<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class BrainController extends Controller
{
	private $greetings = ["hola", "que tal", "hey", "como estas", "como te va"];
	private $questions = ["como", "cuando", "donde", "por que", "porque", "quien", "quienes"];
	private $requests  = ["define", "interpreta", "busca", "diagnostica", "diagnostico", "dame", "recuerdame", "avisame", "reproduce", "llama", "responde", "silencio"];

    public function getResponse($userText)
    {
    	$userWords = explode(" ", strtolower($userText) );

    	foreach($userWords as $userWord)
    	{
            //greeting
    		if( $this->isGreeting( $userWord ) ){

    			return $this->greetingResponse();
    		}

            //request
            if( $this->isRequest( $userWord ) ){
                $req = str_replace("oye karlo", "", $userText);
                $req = str_replace("karlo", "", $userText);
                $req = str_replace("dame ", "", $req);
                if($req == "el clima")
                {
                       //haces lo de la api aqui
                    $client = new Client();
                    $send = $client->request('GET','https://weather.cit.api.here.com/weather/1.0/report.json?product=observation&name=Monterrey&app_id=DemoAppId01082013GAL&app_code=AJKnXv84fjrb0KIHawS0Tg',['verify' => false]);
                    $api_response = $send->getBody();
                    return $api_response;
                }
            }

    	}
    	return "Lo siento, no te entendi.";
    }

    private function isGreeting( $userWord )
    {
    	foreach($this->greetings as $greeting)
    	{
    		if($userWord == $greeting)
    			return true;
    	}
    	return false;
    }

    private function isRequest( $userWord )
    {
        foreach($this->requests as $request)
        {
            if($userWord == $request)
                return true;
        }
        return false;
    }

    private function greetingResponse() {
    	$randomNumber = rand(0, (sizeof($this->greetings) - 1 ) );

    	return $this->greetings[ $randomNumber ];

    }




}
