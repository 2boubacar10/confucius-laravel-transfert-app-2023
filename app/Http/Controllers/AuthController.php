<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request){

        //logique d'authentification

        //validation des données reçues
        $donnees = $request->validate([
            "email" => "required",
            "mot_de_passe" => "required"
        ]);

        $email = $donnees["email"];

        $user = User::where('email', $email)->first();

        //vérification si on trouve un utilisateur avec l'adresse email saisie
        if(!$user){
            return response()->json(
                [
                    "status" => "failed",
                    "data" => [
                    ],
                    "message" => "Adresse email introuvable dans notre application"
                ],
                404
            );
        }

        if(!Hash::check($donnees["mot_de_passe"], $user->password)){
            return response()->json(
                [
                    "status" => "failed",
                    "data" => [
                    ],
                    "message" => "L'email ou le mot de passe saisi est incorecte. Veuillez réésayer"
                ],
                404
            );
        }



        $token = $user->createToken("TransfertArgent")->plainTextToken;

        return response()->json([
            "status" => "success",
            "data" => [
                "token" => $token,
                "user" => $user
            ],
            "message" => "Authentication réussie avec succès"
        ]);
    }

    public function inscription(Request $request){
        //logique d'inscription d'un utilisateur

        //validation des données reçues
        $donnees = $request->validate([
            "name" => "required",
            "email" => "required|unique:users,email",
            "password" => "required"
        ]);

        $user = User::create([
            "name" => $donnees["name"],
            "email" => $donnees["email"],
            "password" => bcrypt($donnees["password"])
        ]);

        $token = $user->createToken("TransfertArgent")->plainTextToken;

        return response()->json(
            [
                "status" => "success",
                "data" => [
                    "token" => $token,
                ],
                "message" => "Inscription réussie avec succès"
            ]
        );
    }


    public function getUsers(){
        $users = User::all();
        return response()->json($users);
    }
}
