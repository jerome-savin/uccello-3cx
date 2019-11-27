<?php

namespace JeromeSavin\Uccello3cx\Http\Controllers;

use App\Contact;
use Illuminate\Http\Request;
use Uccello\Core\Models\Domain;
use Uccello\Core\Http\Controllers\Core\Controller as UccelloController;

class ContactController extends UccelloController
{
    function retriveContact($domain, $number, Request $request)
    {
        app('debugbar')->disable();
        $contact = Contact::where('phone', $number)
            ->orWhere('mobile', $number)
            ->with('organisation')
            ->first();
        if(!$contact)
            return null;

        return $contact->toJson();
    }

    function addContact($domain, Request $request)
    {
        app('debugbar')->disable();
        $domain = ucdomain($domain);
        $contact = new Contact();
        $data = $request->only($contact->getFillable());
        $contact->domain_id = $domain->id;
        $contact->fill($data)->save();
        return $contact->toJson();
    }
}
