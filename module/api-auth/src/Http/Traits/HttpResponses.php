<?PHP

namespace Cortexitsolution\ApiAuth\Http\Traits;

use Illuminate\Http\Exceptions\HttpResponseException;

trait HttpResponses
{
    public function sendSuccess($message = null, $data = [], $code = 200)
    {
        $response = [
            "status" => $code,
            "message" => $message,
            "data" => $data
        ];

        return response()->json($response, $code);
    }

    public function sendError($message = null, $errors = [], $code = 401)
    {
        $response = [
            "status" => $code,
            "message" => $message,
            "error" => $errors
        ];
        return response()->json($response, $code);
        // throw new HttpResponseException(response()->json($response, $code));
    }

    public function sendHttpResponseException($message = null, $exception = [], $code = 401)
    {
        $response = [
            "status" => $code,
            "message" => $message,
            "error" => $exception
        ];
        
        throw new HttpResponseException(response()->json($response,$code));
    }
}