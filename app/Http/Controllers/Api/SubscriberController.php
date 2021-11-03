<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GiftCampaign;
use App\Models\Subscriber;
use App\Models\GiftItem;

class SubscriberController extends Controller
{
    public function store()
    {
        $giftCampaign = GiftCampaign::findOrFail(request('id'));
        foreach($giftCampaign->itemCarts as $itemCart) {
            $giftItem = GiftItem::firstOrFail()->where('id', '=', $itemCart->id)->first();
            if ($giftItem->unit_owned >= $itemCart->pivot->amount){
                $giftItem->unit_owned -= $itemCart->pivot->amount;
                $giftItem->save();
            }else {
                return  response()->json(['error' => 'Error msg'], 422);
            }
        };

        $subscriber = Subscriber::create([
            'user_id' => auth()->id(),
            'gift_campaign_id' => $giftCampaign->id,
        ]);
        
        return $subscriber;
    }

    public function destroy($id) 
    {
        $giftCampaign = auth()->user()->subscribers()->findOrFail($id);

        $giftCampaign->subscribers()->detach(auth()->id());

        foreach($giftCampaign->itemCarts as $itemCart) {
            $giftItem = GiftItem::firstOrFail()->where('id', '=', $itemCart->id)->first();
            $giftItem->unit_owned += $itemCart->pivot->amount;
            $giftItem->save();
        };
        return response('', 204);
    } 

    
}
