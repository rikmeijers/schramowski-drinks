<?php

namespace App\Support;

class RentalOrderItemCatalog
{
    /**
     * Canonical item labels used for UI (modal), detail view and print.
     * Keys must match the form input keys stored in rental_orders.items.
     */
    public static function labels(): array
    {
        return [
            // Tap & Zubehör
            'zapfanlage' => 'Zapfanlage/-n',
            'transportkist' => 'graue Transportkiste',
            'kegs' => 'Keg/-s',
            'drukmeter' => 'Druckmesser',
            'slangen' => 'Schläuche',
            'afdruipbak' => 'Abtropfschale',
            'sleutel' => 'Schlüssel',
            'co2' => 'CO₂',

            // Glaswerk
            'bierglazen' => 'Biergläser',
            'wijnglazen' => 'Weingläser',
            'schnapsglazen' => 'Schnapsgläser',
            'sektglazen' => 'Sektgläser',
            'kolschglazen' => 'Kölschgläser',

            // Mobiliar & Kühlung
            'statafels' => 'Stehtisch/-e',
            'bankgarnituren' => 'Bankgarnitur/-en',
            'asbakken' => 'Aschenbecher',
            'dienbladen' => 'Tablett/-s',
            'koelkast' => 'Kühlschrank',
            'koeltruhe' => 'Kühltruhe',
            'koelwagen' => 'Kleiner Kühlwagen',

            // Spülset
            'spueltheke' => 'Spültheke/-n',
            'spuel_schlauch' => 'Schlauch',
            'spuel_dreieck' => 'goldenes Dreieck',
            'spuel_stopfen' => 'Stopfen',
            'spuel_wasserhahn' => 'Wasserhahn',
            'spuel_gardena' => 'Gardena Anschluss',
            'arbeitstheke' => 'Arbeitstheke/-n',

            // (Pfandschein items removed)
        ];
    }

    public static function labelFor(string $key): string
    {
        $labels = static::labels();
        return $labels[$key] ?? $key;
    }
}

