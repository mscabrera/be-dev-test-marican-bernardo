<?php

namespace App\BusinessServices;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;


/**
 * Class Customers.
 * 
 * Handles all the logic related to customers.
 * 
 */
class Customers
{
    /**
     * Default batch size when parsing and inserting customer data.
     */
    const CHUNK_SIZE = 100;


    /**
     * Fetches and returns list of customers.
     * 
     * @param  array $aParams
     * @return Customer[]
     */
    public function listCustomers(array $aParams)
    {
        $iPage = 1;
        if (isset($aParams['page']) === true) {
            $iPage = (int) $aParams['page'];
        }

        $iItemsPerPage = 10;
        if (isset($aParams['per_page']) === true) {
            $iItemsPerPage =  (int) $aParams['per_page'];
        }


        return Customer::query()->paginate($iItemsPerPage, ['*'], 'page', $iPage);
    }

    /**
     * Import the customer data from the CSV file into the table.
     * 
     * @param  File $oFile
     * @return void
     */
    public function importCustomers($oFile): void
    {
        try {
            $oFileResource = fopen($oFile->getRealPath(), 'r');
            if ($oFileResource === false) {
                throw new Exception('Unable to open file.');
            }

            $aCsvHeaders = fgetcsv($oFileResource);

            $aChunkedData = [];
            while (($aCsvRow = fgetcsv($oFileResource)) !== false) {
                if (empty(array_filter($aCsvRow)) === true) {
                    continue;
                }

                $aChunkedData[] = $this->formatCustomerData($aCsvHeaders, $aCsvRow);

                if (count($aChunkedData) >= self::CHUNK_SIZE) {
                    $this->insertCustomerData($aChunkedData);
                    // Reset chunk
                    $aChunkedData = [];
                }
            }

            // Insert chunked data that is less than the defined chunk size
            if (count($aChunkedData) > 0) {
               $this->insertCustomerData($aChunkedData); 
            }

            fclose($oFileResource);
        } catch (\Exception $oException) {
            throw $oException;
        }
    }

    /**
     * Inserts customer data into the database.
     * 
     * @param  array  $aCustomers
     * @return void
     */
    private function insertCustomerData(array $aCustomers): void
    {
        // dump($aCustomers);
        DB::beginTransaction();
        try {
            Customer::insert($aCustomers);
            DB::commit();
        } catch (Exception $oException) {
            dump('rollback');
            DB::rollBack();
            throw $oException;
        }
    }

    /**
     * Returns formatted customer data.
     * 
     * @param  array  $aCsvHeaders CSV header
     * @param  array  $aRow        Parsed data
     * @return array 
     */
    private function formatCustomerData(array $aCsvHeaders, array $aRow): array
    {
        $aRowData = array_combine($aCsvHeaders, $aRow);

        return [
            'first_name' => $aRowData['first_name'] ?? null,
            'last_name'  => $aRowData['last_name'] ?? null,
            'email'      => $aRowData['email'] ?? null,
            'gender'     => $aRowData['gender'] ?? null,
            'ip'         => $aRowData['ip_address'] ?? null,
            'company'    => $aRowData['company'] ?? null,
            'city'       => $aRowData['city'] ?? null,
            'title'      => $aRowData['title'] ?? null,
            'website'    => $aRowData['website'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}