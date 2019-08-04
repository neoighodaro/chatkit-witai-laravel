<?php

namespace App;

use Carbon\Carbon;
use Jeylabs\Wit\Wit as BaseWit;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;

class Wit extends BaseWit
{
    const WIT_API_VERSION = '20190715';

    protected $supportedIntents = [
        'Agent', 'book'
    ];

    protected function makeRequest($method, $uri, $query = [], $data = [])
    {
        $query = array_merge($query, ['v' => static::WIT_API_VERSION]);

        return parent::makeRequest($method, $uri, $query, $data);
    }

    public function handleCustomerQuery(Collection $response, array $customer, $roomId)
    {
        $hasGreeting = $response->has('greetings');

        $intent = $response->has('intent')
            ? collect($response->get('intent'))->sortByDesc('confidence')->first()['value']
            : false;

        if ($intent && !in_array($intent, $this->supportedIntents)) {
            $intent = false;
        }

        $dateTime = $response->has('datetime')
            ? new Carbon($response->get('datetime')[0]['value'])
            : false;

        if ($dateTime && !$dateTime->isAfter(now()->addDay())) {
            $dateTime = false;
        }

        if ($intent) {
            return $intent === 'book'
                ? $this->handleBooking($dateTime, $customer)
                : $this->handleAgentTransfer($roomId);
        }

        if ($hasGreeting) {
            return Arr::random([
                'Hello, how may I help you?',
                'Hi, how can I help you?'
            ]);
        }

        return 'Sorry, I could not understand you, try again or say "You would like to speak to an Agent"';
    }

    protected function handleBooking($date, array $customer)
    {
        $date = $date ?: now()->addDay();

        Booking::create(['customer_id' => $customer['id'], 'appointment' => $date]);

        $suffix = $date->diffForHumans() . '. Anything else?';

        return Arr::random([
            'Your booking has been made and it\'s ' . $suffix,
            'Your booking has been placed sucessfully and it\'s ' . $suffix,
        ]);
    }

    protected function handleAgentTransfer($roomId)
    {
        Room::findOrFail($roomId)->update(['handled_by_bot' => 0]);

        return Arr::random([
            'An agent will be with you shortly...',
            'Hang on while we find an agent for you...',
            'The chat will be redirected to an agent...',
            'All our agents are busy now, but one will be with you soon...',
        ]);
    }
}
