<?php

function getFileContent($filename)
{
    $content = file_get_contents($filename);

    if ($content === false) {
        die("Неудалось открыть файл.");
    }

    return $content;
}

function createFile($filename, &$content)
{
    if (!empty($filename)) {
        if (!$handle = fopen($filename, 'w+')) {
            die("Неудается создать файл ($filename)");
        }

        if (fwrite($handle, $content) === false) {
            die("Неудается записать в файл ($filename)");
        }

        fclose($handle);
    } else {
        die("Нужно указать имя файла.");
    }
}

function task1($filename)
{
    $xmlContent = getFileContent($filename);
    $htmlBlank = getFileContent("blank.html");

    $xml = new SimpleXMLElement($xmlContent);

    $html = str_replace([
        '{ORDER_NUMBER}',
        '{ORDER_DATE}',
        '{SHIP_NAME}',
        '{SHIP_STREET}',
        '{SHIP_CITY}',
        '{SHIP_ZIP}',
        '{SHIP_COUNTRY}',
        '{BILL_NAME}',
        '{BILL_STREET}',
        '{BILL_CITY}',
        '{BILL_ZIP}',
        '{BILL_COUNTRY}',
        '{ITEM1_NUMBER}',
        '{ITEM1_NAME}',
        '{ITEM1_QUANTITY}',
        '{ITEM1_PRICE}',
        '{ITEM1_OTHER}',
        '{ITEM2_NUMBER}',
        '{ITEM2_NAME}',
        '{ITEM2_QUANTITY}',
        '{ITEM2_PRICE}',
        '{ITEM2_OTHER}',
        '{DELIVERY_NOTES}'
    ], [
        $xml['PurchaseOrderNumber'],
        date("m/d/Y", strtotime($xml['OrderDate'])),
        $xml->Address[0]->Name,
        $xml->Address[0]->Street,
        $xml->Address[0]->City,
        $xml->Address[0]->State . ", " . $xml->Address[0]->Zip,
        $xml->Address[0]->Country,
        $xml->Address[1]->Name,
        $xml->Address[1]->Street,
        $xml->Address[1]->City,
        $xml->Address[1]->State . ", " . $xml->Address[1]->Zip,
        $xml->Address[1]->Country,
        $xml->Items->Item[0]['PartNumber'],
        $xml->Items->Item[0]->ProductName,
        $xml->Items->Item[0]->Quantity,
        $xml->Items->Item[0]->USPrice,
        $xml->Items->Item[0]->Comment,
        $xml->Items->Item[1]['PartNumber'],
        $xml->Items->Item[1]->ProductName,
        $xml->Items->Item[1]->Quantity,
        $xml->Items->Item[1]->USPrice,
        $xml->Items->Item[1]->ShipDate,
        $xml->DeliveryNotes
    ], $htmlBlank);

    echo $html;
}

function task2()
{
    $cars = [
        'audi' => [
            '100',
            'A4',
            'Q7'
        ],
        'bmw' => [
            'X5',
            'X6',
            'Z4'
        ]
    ];

    createFile("output.json", json_encode($cars));

    $cars2 = (array) json_decode(getFileContent("output.json"));

    if (rand(0, 100) > 50) {
        $cars2['audi'][2] = 'A5';
        $cars2['bmw'][0] = 'X3';
    }

    createFile("output2.json", json_encode($cars2));

    $newCars = (array) json_decode(getFileContent("output.json"));
    $newCars2 = (array) json_decode(getFileContent("output2.json"));

    $isSame = true;

    foreach ($newCars as $key => $data) {
        for ($i = 0; $i < count($data); $i++) {
            if ($newCars[$key][$i] != $newCars2[$key][$i]) {
                $isSame = false;
                echo "Значения в массивах с ключем '$key' и индексом [$i] не совпадают!<br />";
            }
        }
    }

    if ($isSame) {
        echo "Массивы полностью совпадают.";
    }
}

function task3()
{
    $array = [];

    for ($i = 0; $i < 50; $i++) {
        $array[] = rand(1, 100);
    }

    $filename = 'numbers.csv';

    if (!$handle = fopen($filename, 'w+')) {
        die("Неудается создать файл ($filename)");
    }

    if (fputcsv($handle, $array) === false) {
        die("Неудается записать в файл ($filename)");
    }

    fclose($handle);

    $sum = 0;

    if (($handle = fopen($filename, "r")) !== false) {
        while (($numbers = fgetcsv($handle, 4096, ",")) !== false) {
            if (is_array($numbers)) {
                for ($i = 0; $i < count($numbers); $i++) {
                    if ($numbers[$i] % 2 == 0) {
                        $sum += $numbers[$i];
                    }
                }
            }
        }

        fclose($handle);
    }

    echo "Сумма цетных чисел равна $sum";
}

function task4()
{
    $link = "https://en.wikipedia.org/w/api.php?action=query&titles=Main%20Page&prop=revisions&rvprop=content&".
        "format=json";

    $ob = json_decode(getFileContent($link));

    echo "Title: " . $ob->query->pages->{'15580374'}->title . "<br />";
    echo "Page ID: " . $ob->query->pages->{'15580374'}->pageid;
}
