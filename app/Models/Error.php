<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Http\JsonResponse;

class Error extends Model
{
    use HasFactory;

    public string $title;
    public string $description;
    public int $http;

    public function __construct(string $title, string $description = '', int $http = 400)
    {
        $this->title = $title;
        $this->description = $description;
        $this->http = $http;
    }

    /**
     * Returns this error as a HTTP response
     * @return \Illuminate\Http\JsonResponse
     */
    public function toHTTPresponse(): JsonResponse
    {
        return response()->json(['error' => $this->title], $this->http);
    }
}
