<?php

/**
 * This Class is responsible for all the calculation related to quotation
 */

class QuotationCalculator
{
    private $number_currency;
    private $number_decimal;
    private $total_tva;
    private $total_ht;
    private $total_ttc;
    private $total_nbPersons;

    public function __construct()
    {
        $this->number_currency = new NumberFormatter("fr_FR", NumberFormatter::CURRENCY);
        $this->number_decimal = new NumberFormatter("fr_FR", NumberFormatter::DECIMAL);
        $this->number_decimal->setAttribute(NumberFormatter::FRACTION_DIGITS, 2);
        $this->total_tva = 0;
        $this->total_ht = 0;
        $this->total_ttc = 0;
        $this->total_nbPersons = 0;
    }

    public function calculatePrices($person_data, $TVA, $DISCOUNT)
    {
        foreach ($person_data as $person) {
            $age_data = getAgeById($person->age_id);
            $unit_ht = ($age_data->price / (1 + ($TVA / 100)));
            $unit_ttc = $age_data->price;
            $amount_ht = ($age_data->price / (1 + ($TVA / 100))) * ($person->nbPersons);
            $amount_ttc = ($age_data->price) * ($person->nbPersons);
            $amount_tva = $amount_ttc - $amount_ht;
            $this->total_tva += $amount_tva;
            $this->total_ht += $amount_ht;
            $this->total_ttc += $amount_ttc;
            $this->total_nbPersons += $person->nbPersons;
        }

        if ($this->total_nbPersons >= 15) {
            $age_list = getAgeList();
            $free_person = 1;
            $add_free_person = floor(($this->total_nbPersons - 15) / 10);
            $total_free_persons = $free_person + $add_free_person;
            $discount_amount_ht = ($unit_ht - (($unit_ht * $DISCOUNT) / 100)) * $total_free_persons;
            $discount_amount_ttc = ($unit_ttc - (($unit_ttc * $DISCOUNT) / 100)) * $total_free_persons;
            return [
                'total_ht' => $discount_amount_ht,
                'total_ttc' => $discount_amount_ttc
            ];
        }

        return [
            'total_ht' => $this->total_ht,
            'total_ttc' => $this->total_ttc
        ];
    }

    public function getTotalHTFormatted()
    {
        return $this->number_currency->format($this->total_ht);
    }

    public function getTotalTVAFormatted()
    {
        return $this->number_currency->format($this->total_tva);
    }

    public function getTotalTTCFormatted()
    {
        return $this->number_currency->format($this->total_ttc);
    }
}
