<?PHP

namespace App\Traits;
use Illuminate\Http\Exceptions\HttpResponseException;

trait HttpResponses
{
    /**
     * send success response
     */
    public function sendSuccess($message = null, $data = [], $code = 200)
    {
        return response()->json(
            [
                "code" => $code,
                "status" => "success",
                "message" => $message,
                "data" => $data
            ],
            $code
        );
    }
    /**
     * send error response
     */
    public function sendError($message = null, $error = [], $code)
    {
        return response()->json(
            [
                "code" => $code,
                "status" => 'error',
                "message" => $message,
                "error" => $error
            ],
            $code
        );
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