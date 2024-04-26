<?php

/**
 * This Class is responsible for the calculations in the quotation
 */

// Define constants for calculation
define('TVA', 5.50);
define('DISCOUNT', 100.00); // the percentage of discount can be modified here

class QuoteCalculator
{
    public function __construct()
    {
    }

    public function calculateResults($quote_data, $person_data)
    {
        // Initialize variables
        $total_tva = 0;
        $total_ht = 0;
        $total_ttc = 0;
        $total_paying_persons = 0;
        $total_persons = 0;
        $unit_ht = [];
        $amount_ht = [];
        $amount_ttc = [];
        $ref = "";
        $guided_qty = 0;
        $guided_price_ht = 0;
        $guided_amount_ht = 0;
        $guided_amount_ttc = 0;
        $total_free_persons = 0;
        $discount_unit_ht = 0;
        $discount_amount_ht = 0;
        $discount_amount_ttc = 0;

        // Calculate total number of paying persons
        foreach ($person_data as $person) {
            $age_data = getAgeById($person->age_id);
            $total_paying_persons += $age_data->id === '1' ? 0 : $person->nbPersons; // update total number of paying person, excluding the age category 1 (age less than 3 years old)
            $total_persons += $person->nbPersons; // update total number of persons
        }

        // Calculate prices
        foreach ($person_data as $person) {
            $age_data = getAgeById($person->age_id); // ger one row of age data in the current quote

            if ($total_paying_persons < 15) {
                $unit_ttc = $age_data->price; // one unit price with tax at normal rate
                $ref = $age_data->ref; // reference for the normal category
            } else {
                $unit_ttc = $age_data->price_disc; // one unit price with tax at discounted rate
                $ref = $age_data->ref_disc; // reference for the discounted category
            }

            $unit_ht[] = ($unit_ttc / (1 + (TVA / 100))); // one unit price without tax
            $amount_ht[] = ($unit_ttc / (1 + (TVA / 100))) * ($person->nbPersons); // full price based on the number of person without tax
            $amount_ttc[] = $unit_ttc * $person->nbPersons; // full price based on the number of person with tax
            $amount_tva = ($unit_ttc / 100) * TVA * $person->nbPersons; // full amount of the tax

            $total_tva += $amount_tva; // update total tax
            $total_ht += $amount_ht[count($amount_ht) - 1]; // Update total price without tax (accessing the last elements of the respective arrays using count($array) - 1)
            $total_ttc += $amount_ttc[count($amount_ttc) - 1]; // Update total price with tax
        }

        // Calculate guided price
        $visitetype_guided = getVisiteTypeById($quote_data->visitetype_id); // run a query in the database to get the guided category row

        $guided_price_ttc = $visitetype_guided->price;
        $guided_price_ht = $guided_price_ttc / (1 + (TVA / 100)); // Calculate the guided price without tax

        // Calculate the full guided price depending on the number of people
        if ($total_paying_persons <= 10) {
            $guided_amount_ttc = $guided_price_ttc;
            $guided_qty = 1;
        } else if ($total_paying_persons > 10 && $total_paying_persons <= 20) {
            $guided_amount_ttc = $guided_price_ttc * 2;
            $guided_qty = 2;
        } else if ($total_paying_persons > 20) {
            $guided_amount_ttc = $guided_price_ttc * 3;
            $guided_qty = 3;
        }; // price with tax

        $guided_amount_ht = $guided_amount_ttc / (1 + (TVA / 100)); // Calculate the guided price with tax
        $guided_amount_tva = $guided_amount_ttc - $guided_amount_ht; // Calculate tax for the guided price

        $total_tva += $guided_amount_tva; // Update total tax with the added guided price
        $total_ht += $guided_amount_ht; // Update total price without tax with the added guided price
        $total_ttc += $guided_amount_ttc; // Update total price with tax with the added guided price


        // Calculate free adult person beyond 15 paying persons
        $free_person = 1; // The initial free person for the first 15 persons.

        // For every additional 10 persons beyond the initial 15, add another free person.
        $add_free_person = floor(($total_paying_persons - 15) / 10); // The floor function rounds down to the nearest whole number.
        $add_free_person = floor(-1.4); // The floor function rounds down to the nearest whole number.

        $total_free_persons = $free_person + $add_free_person; // Total number of free persons

        $total_persons += $total_free_persons > 0 ? $total_free_persons : 0; // update total number of persons with the additionam free adult persons if exists

        // calculate discounted prices for adult category
        $age_list = getAgeList(); // run a query in the database to get the category
        $discount_unit_ttc = $age_list[2]->price_disc; // discounted adult unit price with tax
        $discount_unit_ht = ($discount_unit_ttc / (1 + (TVA / 100))); // one discounted adult unit price without tax

        $discount_amount_ht = ($discount_unit_ht - (($discount_unit_ht * DISCOUNT) / 100)) * $total_free_persons;
        $discount_amount_ttc = ($discount_unit_ttc - (($discount_unit_ttc * DISCOUNT) / 100)) * $total_free_persons;
        $discount_amount_tva = $discount_amount_ttc - $discount_amount_ht;

        $total_tva += $discount_amount_tva; // Update total tax with the added discount price
        $total_ht += $discount_amount_ht; // Update total price without tax with the added discount price
        $total_ttc += $discount_amount_ttc; // Update total price with tax with the added discount price


        return [
            'total_tva' => $total_tva,
            'total_ht' => $total_ht,
            'total_ttc' => $total_ttc,
            'total_paying_persons' => $total_paying_persons,
            'total_persons' => $total_persons,
            'unit_ht' => $unit_ht,
            'amount_ht' => $amount_ht,
            'amount_ttc' => $amount_ttc,
            'ref' => $ref,
            'guided_qty' => $guided_qty,
            'guided_price_ht' => $guided_price_ht,
            'guided_amount_ht' => $guided_amount_ht,
            'guided_amount_ttc' => $guided_amount_ttc,
            'total_free_persons' => $total_free_persons,
            'discount_unit_ht' => $discount_unit_ht,
            'discount_amount_ht' => $discount_amount_ht,
            'discount_amount_ttc' => $discount_amount_ttc,
        ];
    }
}
