<?php

namespace JeromeSavin\Uccello3cx\Http\Controllers;

use App\Contact;
use JeromeSavin\Uccello3cx\Models\CallEvent;
use Illuminate\Http\Request;
use Uccello\Core\Models\Domain;
use Uccello\Core\Http\Controllers\Core\Controller as UccelloController;

class ContactController extends UccelloController
{
    function retriveContact($domain, $number, Request $request)
    {
        app('debugbar')->disable();
        $contacts = Contact::where('phone', 'LIKE', '%'.$number.'%')
            ->orWhere('mobile', 'LIKE', '%'.$number.'%')
            ->with('organisation')
            ->get();
        if(count($contacts)<1)
            return null;

        return $contacts->toJson();
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

    function addCallEvent($domain, Request $request)
    {
        app('debugbar')->disable();
        $domain = ucdomain($domain);

        if($request->has('contact'))
            $contact = Contact::find($request->get('contact'));

        $callEvent = new CallEvent;
        $callEvent->type = $request->get('type');
        $callEvent->contact_id = $contact->id ?? null;
        $callEvent->agent = $request->get('agent');
        $callEvent->duration = $request->get('duration');
        $callEvent->domain_id = $domain->id;

        $callEvent->save();
    }
}
