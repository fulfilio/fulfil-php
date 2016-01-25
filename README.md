# Fulfil.IO PHP Api Client

This API client allows developers to use the REST API of Fulfil.IO

## Quickstart

```php

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

```
