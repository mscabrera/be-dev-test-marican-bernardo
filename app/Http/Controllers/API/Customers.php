<?php

namespace App\Http\Controllers\API;

use App\BusinessServices;
use App\Http\Controllers\Controller;
use App\Models;
use Illuminate\Http\Request;

class Customers extends Controller
{
    /**
     * @var BusinessServices\Customer
     */
    protected $oCustomerService;

    /**
     * Customer __construct.
     * 
     * @param BusinessServices\Customer $oCustomerService
     */
    public function __construct(BusinessServices\Customers $oService)
    {
        $this->oCustomerService = $oService;
    }

    /**
     * Returns list of customers with pagination.
     * 
     * @param  Request $request 
     * @return Customers[]|null
     */
    public function index(Request $oRequest)
    {
        return response()->json($this->oCustomerService->listCustomers($oRequest->all()));
    }

    /**
     * Imports CSV file 
     * 
     * @param  Request $oRequest 
     * @return object            
     */
    public function import(Request $oRequest)
    {
        try {
            $oFile = $this->isFileValid($oRequest);

            $this->oCustomerService->importCustomers($oRequest->file('file'));
            return response()->json(['message' => 'Successfully imported customer data']);
        } catch (Error $oException) {
            return response()->json(['message' => $oException->getMessage()], 500);
        }
    }

    /**
     * Returns true when the uploaded file pass otherwise, false.
     * 
     * @param  Request $oRequest 
     * @return boolean           
     */
    private function isFileValid(Request $oRequest)
    {
        return $oRequest->validate(['file' => 'required|file|mimes:csv,txt']);
    }
}