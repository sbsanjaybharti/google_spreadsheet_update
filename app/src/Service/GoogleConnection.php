<?php
namespace App\Service;

class GoogleConnection
{
    protected $service;
    protected $spreadsheetId;

    public function __construct($credentials_json, $spreadsheetId)
    {
        $this->spreadsheetId = $spreadsheetId;
        $client = new \Google_Client();
        $client->setApplicationName('Google Sheets API PHP');
        $client->setScopes(\Google_Service_Sheets::SPREADSHEETS);
        $client->setAuthConfig($credentials_json);
        $client->setAccessType('offline');
        $this->service = new \Google_Service_Sheets($client);

    }

    public function get($range){
        return $this->service->spreadsheets_values->get($this->spreadsheetId, $range);
    }

    public function insert_row($values, $range){

        $body = new \Google_Service_Sheets_ValueRange(['values' => array($values)]);
        $this->service->spreadsheets_values->append(
            $this->spreadsheetId,
            $range,
            $body,
            ['valueInputOption' => 'RAW'],
            ['insertDataOption' => "INSERT_ROWS"]

        );
    }

}