<?php 
namespace App\Libs;

class Emissionco2 {

    public static function km_to_emision_kg($km){
        /*
        Carro: A emissão de CO2 por quilômetro percorrido em um carro pode variar com base no tipo de veículo, no consumo de combustível e em outros fatores. Uma estimativa média geral para carros movidos a gasolina é de cerca de 2,3 kg de CO2 por litro de gasolina queimada. Isso equivale a aproximadamente 0,18 kg de CO2 por quilômetro percorrido, com base em um consumo médio de 12 km/l.

        Avião: A emissão de CO2 por quilômetro voado em um avião pode variar dependendo do tipo de aeronave, eficiência do voo e outros fatores. Uma estimativa média é de cerca de 0,15 a 0,25 kg de CO2 por passageiro por quilômetro.

        Trem (elétrico): A emissão de CO2 por quilômetro percorrido em trens elétricos é geralmente mais baixa do que em carros e aviões. Uma estimativa média é de cerca de 0,03 a 0,08 kg de CO2 por passageiro por quilômetro para trens elétricos.

        */
        $modals = [
            'by_car' => 0.18 * (int) $km, 
            'by_airplane' => 0.25 * (int) $km,
            'by_train' => 0.08 * (int) $km,
        ];
        $kg_of_CO2 = array_sum($modals) / count(array_filter($modals));
        return number_format($kg_of_CO2, 2, '.', '');
    }

    public static function distance($lat1, $lon1, $lat2, $lon2, $unit="K") {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1))
            * sin(deg2rad($lat2))
            +  cos(deg2rad($lat1))
                * cos(deg2rad($lat2))
                * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);
        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

}