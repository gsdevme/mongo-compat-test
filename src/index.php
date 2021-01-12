<?php

require_once realpath(__DIR__).'/../vendor/autoload.php';

use Symfony\Component\VarDumper\VarDumper;

# due to https://stackoverflow.com/questions/47569871/aggregatecursor-issue-with-mongodb-3-6
# https://derickrethans.nl/64bit-ints-in-mongodb.html
ini_set('mongo.native_long', false);
ini_set('mongo.long_as_object', false);

$mongo = new MongoClient('mongodb://root:password@mongo/');
$mongo->connect();

$db = $mongo->selectDB('wibble');
$result = $db->command(['serverStatus' => true]);

VarDumper::dump(sprintf('Mongo Version %s', $result['version']));

$mongo->listDBs();

$database = 'test';
$collectionName = 'testing123';

$collection = $mongo->selectCollection($database, $collectionName);
$collection->deleteIndexes();
$collection->remove([]);

$collection->batchInsert(
    [
        [
            ['data' => 5],
            ['data' => 10],
            ['data' => 15],
        ],
        [
            ['data' => 7],
            ['data' => 77],
            ['data' => 774],
        ],
        [
            ['data' => 838],
            ['data' => 8889],
            ['data' => 888],
        ],
    ]
);

assert(
    iterator_count($collection->find()) == 3,
    '3 items exist in the collection'
);

$collection->remove([]);

assert(
    iterator_count($collection->find()) == 0,
    '0 items exist in the collection'
);

$collection->insert(['ping' => 'pong', 'hello' => 'world', 'weather' => 'snow']);

assert(
    $collection->findOne(['ping' => 'pong'])['hello'] == 'world',
    'Hello world'
);

assert(
    !array_key_exists('hello', $collection->findOne(['ping' => 'pong'], ['weather' => 1])),
    'projection works because hello array key is missing'
);

$collection->remove([]);

$collection->batchInsert(
    [
        [
            'customer' => 'bob',
            'type' => 'add',
            'amount' => 10,
        ],
        [
            'customer' => 'bob',
            'type' => 'add',
            'amount' => 10,
        ],
        [
            'customer' => 'harry',
            'type' => 'add',
            'amount' => 10,
        ],
        [
            'customer' => 'bob',
            'type' => 'divide',
            'amount' => 5,
        ],
    ]
);

$result = $collection->aggregateCursor(
    [
        ['$group' => ['_id' => '$customer', 'total' => ['$sum' => '$amount']]],
        ['$sort' => ['total' => -1]],
    ]
);

$legacyResult = [
    'result' => iterator_to_array($result),
    'ok' => 1,
];

assert(
    $legacyResult == [
        'result' =>
            [

                [
                    '_id' => 'bob',
                    'total' => 25,
                ],

                [
                    '_id' => 'harry',
                    'total' => 10,
                ],
            ],
        'ok' => 1,
    ],
    'Mongo aggregateCursor mapped into legacy structure'
);

VarDumper::dump(sprintf('Yup all worked'));
