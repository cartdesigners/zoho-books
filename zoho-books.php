<?php
/**
*
* A PHP class that acts as wrapper for Zoho Books API.
*
*
* Copyright 2013 Cart Designers, LLC
* Licensed under the Apache License 2.0
*
* Author: Ransom Carroll [github.com/ransomcarroll]
* Version: 1.0
*
*/
class ZohoBooks {

    private $timeout = 10;
    private $debug = false;
    private $advDebug = false; // Note that enabling advanced debug will include debugging information in the response possibly breaking up your code
    private $zohoBooksApiVersion = "3";
    public $responseCode;

    private $endPointUrl;
    private $apiKey;
    private $contactsUrl;
    private $estimatesUrl;
    private $invoicesUrl;
    private $recurringInvoicesUrl;
    private $creditNotesUrl;
    private $customerPaymentsUrl;
    private $expensesUrl;
    private $recurringExpensesUrl;
    private $billsUrl;
    private $vendorPaymentsUrl;
    private $bankAccountsUrl;
    private $bankTransactionsUrl;
    private $bankRulesUrl;
    private $chartOfAccountsUrl;
    private $journalsUrl;
    private $baseCurrencyAdjustmentUrl;
    private $projectsUrl;
    private $settingsUrl;

    public function __construct($apiKey,$organizationId){

        $this->apiKey = $apiKey;
        $this->organizationId = $organizationId;

        $this->endPointUrl               = "https://books.zoho.com/api/v{$this->zohoBooksApiVersion}/";
        $this->taskUrl                   = $this->endPointUrl."tasks";
        $this->contactsUrl               = $this->endPointUrl."contacts";
        $this->estimatesUrl              = $this->endPointUrl."estimates";
        $this->invoicesUrl               = $this->endPointUrl."invoices";
        $this->recurringInvoicesUrl      = $this->endPointUrl."recurringinvoices";
        $this->creditNotesUrl            = $this->endPointUrl."creditnotes";
        $this->customerPaymentsUrl       = $this->endPointUrl."customerpayments";
        $this->expensesUrl               = $this->endPointUrl."expenses";
        $this->recurringExpensesUrl      = $this->endPointUrl."recurringexpenses";
        $this->billsUrl                  = $this->endPointUrl."bills";
        $this->vendorPaymentsUrl         = $this->endPointUrl."vendorpayments";
        $this->bankAccountsUrl           = $this->endPointUrl."bankaccounts";
        $this->bankTransactionsUrl       = $this->endPointUrl."banktransactions";
        $this->bankRulesUrl              = $this->endPointUrl."bankaccounts/rules";
        $this->chartOfAccountsUrl        = $this->endPointUrl."chartofaccounts";
        $this->journalsUrl               = $this->endPointUrl."journals";
        $this->baseCurrencyAdjustmentUrl = $this->endPointUrl."basecurrencyadjustment";
        $this->projectsUrl               = $this->endPointUrl."projects";
        $this->settingsUrl               = $this->endPointUrl."settings/preferences";

        define("METHOD_POST", 1);
        define("METHOD_PUT", 2);
        define("METHOD_GET", 3);
    }


    /**
     * **********************************
     * Contacts
     * **********************************
     */

    /**
     * Returns the full contact record for a single contact.
     * Call it without parameters to get the users info of the owner of the API key.
     *
     * @param string $contactId
     * @return string JSON or null
     */
    public function getContact($contactId){
        return $this->callZohoBooks($this->contactsUrl."/{$contactId}");
    }

    /**
     * Returns the first 200 contact records for all contact entries.
     *
     * @return string JSON or null
     */
    public function getContacts(){
        return $this->callZohoBooks($this->contactsUrl);
    }

    /**
     * Returns all contact records for all contact entries.
     *
     * @return string JSON or null
     */
    public function getAllContacts(){
        $morePages = true;
        $pageNumber = 1;
        $contactsArray = [];
        while($morePages){
            $result = $this->callZohoBooks($this->contactsUrl,null,METHOD_GET,$pageNumber);
            $contacts = $result->contacts;
            foreach($contacts as $contact){
                array_push($contactsArray,$contact);
            }
            if($result->page_context->has_more_page !== true){
                $morePages = false;
            }
            $pageNumber++;
        }
        return $contactsArray;
    }

    /**
     * Returns all invoice records for a specific contact.
     *
     * @param integer of contact id
     * @return string JSON or null
     */
    public function getContactInvoices($contact_id){
        $params = array(
            'customer_id'=>$contact_id
        );
        $call = $this->callZohoBooks($this->invoicesUrl,null,METHOD_GET,1,$params);
        if($call->message === 'success'){
            return $call->invoices;
        }
    }

    /**
     * Returns all invoice records for a specific contact.
     *
     * @param integer of contact id
     * @param string of invoice status
     * @return string JSON or null
     */
    public function getContactInvoicesByStatus($contact_id,$status){
        $params = array(
            'customer_id'=>$contact_id,
            'status'=>$status
        );
        $call = $this->callZohoBooks($this->invoicesUrl,null,METHOD_GET,1,$params);
        if($call->message === 'success'){
            return $call->invoices;
        }
    }

    /**
     * **********************************
     * Invoices
     * **********************************
     */

    /**
     * Returns an invoices comments and history
     *
     * @param integer of invoice id
     * @return string JSON or null
     */
    public function getInvoiceComments($invoice_id){

        $call = $this->callZohoBooks($this->invoicesUrl.'/'.$invoice_id.'/comments',null,METHOD_GET,1,$params);
        if($call->message === 'success'){
            return $call->comments;
        }
    }

    /**
     * This function communicates with Zoho Books REST API.
     * You don't need to call this function directly. It's only for inner class working.
     *
     * @param string $url
     * @param string $data Must be a json string
     * @param int $method See constants defined at the beginning of the class
     * @param int $page equates to page number for paginating
     * @return string JSON or null
     */
    private function callZohoBooks($url, $data = null, $method = METHOD_GET, $page = 1, $params = false){
        $curl = curl_init();
        if($params){
            $filter = '';
            foreach($params as $key => $value){
                $filter = $filter.'&'.$key.'='.$value;
            }
        }
        curl_setopt($curl, CURLOPT_URL, $url.'?authtoken='.$this->apiKey.'&organization_id='.$this->organizationId.'&page='.$page.$filter);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Don't print the result
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // Don't verify SSL connection
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); //
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json")); // Send as JSON
        if($this->advDebug){
            curl_setopt($curl, CURLOPT_HEADER, true); // Display headers
            curl_setopt($curl, CURLOPT_VERBOSE, true); // Display communication with server
        }
        if($method == METHOD_POST){
            curl_setopt($curl, CURLOPT_POST, true);
        } else if($method == METHOD_PUT){
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
        }
        if(!is_null($data) && ($method == METHOD_POST || $method == METHOD_PUT)){
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }

        try {
            $return = curl_exec($curl);
            $this->responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if($this->debug || $this->advDebug){
                echo "<pre>"; print_r(curl_getinfo($curl)); echo "</pre>";
            }
        } catch(Exception $ex){
            if($this->debug || $this->advDebug){
                echo "<br>cURL error num: ".curl_errno($curl);
                echo "<br>cURL error: ".curl_error($curl);
            }
            echo "Error on cURL";
            $return = null;
        }

        curl_close($curl);

        return json_decode($return);
    }
}