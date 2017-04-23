<?php
/**
 * Created by PhpStorm.
 * @author tomas
 * @package tr
 * Date: 2017-04-22
 * Time: 18:21
 */

$tours = file_get_contents(__DIR__.'/tours.xml');


function xmlToCSV($text) : string
{
    $separator = '|';

    /**
     * @param SimpleXMLElement[] $departures
     * @return string
     * @throws Exception
     */
    $getMinPrice = function (array $departures) : string {
        $minPrice = null;
        foreach ($departures as $dep) {
            $disc = (float)($dep->attributes()['DISCOUNT'] ? str_replace('%', '', $dep->attributes()['DISCOUNT']) : 0); // get discount in float, if exists

            if ($disc > 100) {
                throw new Exception("Discount is over 100!");
            }

            if ($dep->attributes()['EUR']) {
                $price = (float)($dep->attributes()['EUR'] * ((100 - $disc) / 100)); // get price in EUR
            } else {
                throw new Exception("Price in EUR is missing!");
            }

            if ($minPrice === null) {
                $minPrice = $price;
            } elseif ($dep > $price) {
                $minPrice = $price;
            }

        }
        if ($minPrice < 0) { // something fishy
            throw new Exception("Price is below 0, please check prices in import data");
        }

        $minPrice = number_format(round($minPrice,2),2,'.','');
        return $minPrice;
    };

    /**
     * @param string $rawInclusionsText
     * @return string
     */
    $sanitizeInclusions = function($rawInclusionsText) : string
    {
        return trim(
            preg_replace('/\s+/',' ', // strip multiple whitespaces
                strip_tags( // remove HTML
                    html_entity_decode( // decode to HTML
                        str_replace('&nbsp;',' ',$rawInclusionsText), ENT_QUOTES, 'UTF-8' // replace non-breakable bad boys, which cannot be replaced as whitespaces
                    )
                )
            )
        );
    };

    $xml = simplexml_load_string($text);
    $csv = 'Title'.$separator.'Code'.$separator.'Duration'.$separator.'Inclusions'.$separator.'MinPrice'.PHP_EOL;

    foreach ($xml->xpath('TOUR') as $tour){
        $title      = htmlspecialchars_decode($tour->Title, ENT_QUOTES);
        $code       = (string) $tour->Code;
        $duration   = (int) $tour->Duration;
        $inclusions = $sanitizeInclusions($tour->Inclusions);
        $minPrice   = $getMinPrice($tour->xpath('DEP'));
        $csv .= $title.$separator.$code.$separator.$duration.$separator.$inclusions.$separator.$minPrice.PHP_EOL;
    }

    return $csv;
}

echo xmlToCSV($tours);