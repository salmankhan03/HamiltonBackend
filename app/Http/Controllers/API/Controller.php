<?php

namespace App\Http\Controllers\Api;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Response;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    var $success_code = 200;
    /**
     * Method use to send error JSON
     */
    protected function sendJsonErrors($errors, $status = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        return $this->sendJsonResponse(Response::prepareErrorResponse($errors, $status));
    }

    /**
     * Method use to send result data
     */
    protected function sendJson($data = [])
    {
        return $this->sendJsonResponse(Response::prepareResponseOk($data));
    }

    /**
     * Method use to send JSON result
     */
    private function sendJsonResponse($data)
    {
        return response()->json($data);
    }

    /**
     * Method to return collection
     */
    protected function prepareCollection(Collection $collection, $name = 'collection')
    {
        return [
            $name => $collection,
            'count' => $collection->count()
        ];
    }

    /**
     * Method to get API information
     */
    public function getApiInfo()
    {
        return $this->sendJson([
            'version' => config('app.version'),
            'time' => time(),
            'date' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Method to return result json
     * @param Boolean $status = true if success,false if error
     * @param String $msg = Success/Error message
     * @param Array $data = Array containing data to return
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResultJSON($status, $msg = null, $data = array())
    {
        $return_data = array(
            'ResponseCode' => $status,
            'ResponseText' => $msg
        );
        foreach ($data as $key => $value) {
            $return_data[$key] = $value;
        }
        return response()->json($return_data, $this->success_code);
    }
}
