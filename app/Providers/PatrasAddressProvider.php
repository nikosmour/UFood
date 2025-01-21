<?php

namespace App\Providers;

use Faker\Provider\Base;

class PatrasAddressProvider extends Base
{
    protected static $locations = [
        'Ψηλαλώνια' => [
            'streets' => [
                'Αγίου Ανδρέου',
                'Κορίνθου',
                'Νόρμαν',
                'Εθνικής Αντιστάσεως',
                'Κατσαντώνη'
            ],
            'postcodes' => [
                '26221',
                '26222'
            ],
        ],
        'Αγία Σοφία' => [
            'streets' => [
                'Τριών Ναυάρχων',
                'Γούναρη',
                'Καρόλου',
                'Δημητσάνας',
                'Αρτέμιδος'
            ],
            'postcodes' => [
                '26223',
                '26224'
            ],
        ],
        'Άνω Πόλη' => [
            'streets' => [
                'Όθωνος Αμαλίας',
                'Γερμανού',
                'Μιαούλη',
                'Παλαιολόγου',
                'Γοργοποτάμου'
            ],
            'postcodes' => [
                '26331',
                '26332'
            ],
        ],
        'Άγιος Δημήτριος' => [
            'streets' => [
                'Παπαναστασίου',
                'Αρτέμιδος',
                'Κανάρη',
                'Σκουφά',
                'Παπαδιαμαντοπούλου'
            ],
            'postcodes' => [
                '26225',
                '26226'
            ],
        ],
        'Κάτω Βαθιά' => [
            'streets' => [
                'Δερβενακίων',
                'Κολοκοτρώνη',
                'Κουντουριώτου',
                'Κύπρου',
                'Μαυροκορδάτου'
            ],
            'postcodes' => [
                '26333',
                '26334'
            ],
        ],
        'Μποζαΐτικα' => [
            'streets' => [
                'Ανδρούτσου',
                'Πίνδου',
                'Αθηνών',
                'Ζαΐμη',
                'Αλφειού'
            ],
            'postcodes' => [
                '26441',
                '26442'
            ],
        ],
        'Ρίο' => [
            'streets' => [
                'Πατρών-Κορίνθου',
                'Ελευθερίου Βενιζέλου',
                'Ναυαρίνου',
                'Φωκίδος',
                'Σπάρτης'
            ],
            'postcodes' => ['26500'],
        ],
        'Ζαρουχλέικα' => [
            'streets' => [
                'Μαυρομιχάλη',
                'Ρήγα Φεραίου',
                'Απολλωνίου',
                'Θηβών',
                'Καψάλη'
            ],
            'postcodes' => [
                '26335',
                '26336'
            ],
        ],
        'Σκαγιοπούλειο' => [
            'streets' => [
                'Τζαβέλλα',
                'Σολωμού',
                'Δάφνης',
                'Βότση',
                'Δήμου Γούναρη'
            ],
            'postcodes' => [
                '26337',
                '26338'
            ],
        ],
        'Περίβολα' => [
            'streets' => [
                'Αναπαύσεως',
                'Φιλικής Εταιρείας',
                'Μαραθώνος',
                'Κίμωνος',
                'Ανεξαρτησίας'
            ],
            'postcodes' => [
                '26442',
                '26443'
            ],
        ],
    ];


    // Generate a neighborhood
    public static function patrasNeighborhood()
    {
        return static::randomElement(array_keys(static::$locations));
    }

    // Generate a street based on the neighborhood
    public static function patrasStreet($neighborhood)
    {
        return static::randomElement(static::$locations[$neighborhood]['streets']);
    }

    // Generate a postcode based on the neighborhood
    public static function patrasPostcode($neighborhood)
    {
        return static::randomElement(static::$locations[$neighborhood]['postcodes']);
    }

    // Generate a full address
    public static function patrasAddress($buildingNumber)
    {
        $neighborhood = static::patrasNeighborhood();
        $street = static::patrasStreet($neighborhood);
        $postcode = static::patrasPostcode($neighborhood);

        return "{$street} " . $buildingNumber . ", {$neighborhood}, {$postcode}";
    }
}
