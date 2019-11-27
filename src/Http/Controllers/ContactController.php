<?php

namespace JeromeSavin\Uccello3cx\Http\Controllers;

use App\Contact;
use Illuminate\Http\Request;
use Uccello\Core\Models\Domain;
use Uccello\Core\Http\Controllers\Core\Controller as UccelloController;

class ContactController extends UccelloController
{
    // function($number){
    //     app('debugbar')->disable();
    //     return json_encode(array(
    //         'contact' => array(
    //             'id' => 10,
    //             'firstname' => 'FIRSTNAME',
    //             'lastname' => 'LASTNAME',
    //             'company' => 'COMPANY',
    //             'email' => 'EMAIL',
    //             'phone' => 'PHONE',
    //             'info' => 'INFO !'
    //         )
    //     ));
    function retriveContact($domain, $number, Request $request)
    {
        app('debugbar')->disable();
        $contact = Contact::where('phone', $number)
            ->orWhere('mobile', $number)
            ->with('organisation')
            ->first();
        return $contact->toJson();
    }   
}
