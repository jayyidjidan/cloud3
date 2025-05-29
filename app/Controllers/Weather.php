<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Services;

class Weather extends BaseController
{
    private $apiKey;
    private $apiUrl = 'https://api.openweathermap.org/data/2.5/weather';

    public function __construct()
    {
        // Load API key from environment
        $this->apiKey = getenv('OPENWEATHER_API_KEY');
        
        // Initialize the request service
        $this->request = Services::request();
    }

    public function index()
    {
        // Default location (Jakarta)
        return $this->showWeather('Jakarta');
    }

    public function search()
    {
        // Validate input
        $rules = ['city' => 'required|min_length[2]|max_length[50]'];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $city = $this->request->getPost('city');
        return $this->showWeather($city);
    }

    private function showWeather($city)
    {
        $data = $this->getWeatherData($city);
        return view('weather_view', $data);
    }

    private function getWeatherData($city)
    {
        $client = Services::curlrequest();
        
        try {
            $response = $client->get($this->apiUrl, [
                'query' => [
                    'q' => $city,
                    'appid' => $this->apiKey,
                    'units' => 'metric',
                    'lang' => 'id' // Indonesian language
                ],
                'timeout' => 3 // 3 second timeout
            ]);
            
            $weatherData = json_decode($response->getBody(), true);
            
            if ($response->getStatusCode() !== 200) {
                throw new \Exception($weatherData['message'] ?? 'Unknown API error');
            }

            return [
                'city' => $city,
                'temperature' => $weatherData['main']['temp'] ?? 'N/A',
                'humidity' => $weatherData['main']['humidity'] ?? 'N/A',
                'description' => $weatherData['weather'][0]['description'] ?? 'N/A',
                'icon' => $weatherData['weather'][0]['icon'] ?? '',
                'wind_speed' => $weatherData['wind']['speed'] ?? 0,
                'pressure' => $weatherData['main']['pressure'] ?? 0,
                'error' => null,
                'weather_data' => $weatherData // Full data for debugging
            ];
            
        } catch (\Exception $e) {
            log_message('error', 'Weather API Error: ' . $e->getMessage());
            
            return [
                'city' => $city,
                'temperature' => 'N/A',
                'humidity' => 'N/A',
                'description' => 'N/A',
                'icon' => '',
                'wind_speed' => 0,
                'pressure' => 0,
                'error' => 'Gagal mendapatkan data cuaca. Pastikan nama kota benar. (Error: ' . $e->getMessage() . ')',
                'weather_data' => null
            ];
        }
    }

    // Additional API methods
    public function getByCoordinates($lat, $lon)
    {
        $client = Services::curlrequest();
        
        try {
            $response = $client->get($this->apiUrl, [
                'query' => [
                    'lat' => $lat,
                    'lon' => $lon,
                    'appid' => $this->apiKey,
                    'units' => 'metric'
                ]
            ]);
            
            return $this->response->setJSON(json_decode($response->getBody(), true));
            
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'error' => $e->getMessage()
            ]);
        }
    }
}