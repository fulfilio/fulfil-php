<?php
require('config.php');

# Get model for party/contact
$partyModel = new Model('party.party');
$partyData = array(array(
    "name" =>  "Tarun Bhardwaj",
    "addresses" => array(
        array("create", array(
            array(
               "street" => "5118 Brimley Way",
               "city"   => "Sacramento",
               "country" =>  "61",  # Country code in Fulfil
               "subdivision"  => "3556",  # Subdivision code in Fulfil
               "zip" => "95835"
            )
        ))
    ),
    "contact_mechanisms" => array(array("create" , array(
        array("type" =>"phone", "value" =>"9987654321"),
        array("type" =>"email", "value" =>"tarun@fulfil.io")
    )))
));
$party = $partyModel->create($partyData)[0];


$party = $partyModel->get($party['id']);

$parties = $partyModel->search(
    array(
        array('id', '=', $party['id'])
    ),
    array('name')
);
assert((count($parties) == 1));

$partyModel->delete($party['id']);

$parties = $partyModel->search(
    array(
        array('id', '=', $party['id'])
    ),
    array('name')
);
assert((count($parties) == 0));

# Call specific method
#   Calling: party.party.search([])
print_r($partyModel->run(
    'search',  # Method Name
    [          # Array of arguments passed to this method
        []     # Passing domain as empty as 1st argument. this should return all
               #   parties.
    ]
));

# Call specific method with an object as context
#   Calling: (party.party,1).search([])      TODO: Use some better example
print_r($partyModel->run(
    'search',  # Method Name
    [          # Array of arguments passed to this method
        []     # Passing domain as empty as 1st argument. this should return all
               #   parties.
    ],
    1          # Id of the record
));

print "Success";
