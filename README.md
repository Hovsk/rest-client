# rest-client

$resource = new \Api\Client('test', 'www.example.com');

//POST: /cards/1
//{'pin': 1}
$cards = $resource->cards->{1}->post([
    'pin' => 1
]);

//GET: /cards/1/balance
$balance = $resource->cards->{1}->balance->get();

//GET: /countries
$country = $resource->countries->get();

//GET: /cards/1/history?start_time=11&end_time=22
$history = $resource->cards->{1}->history->get([
    'start_time' => 11,
    'end_time' => 22
]);


... and so on
