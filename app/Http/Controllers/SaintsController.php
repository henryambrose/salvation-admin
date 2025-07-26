<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class SaintsController extends Controller
{
    /**
     * Get list of saints for the carousel
     */
    public function index(Request $request): JsonResponse
    {
        $saints = [
            [
                'id' => 1,
                'name' => 'St. Francis of Assisi',
                'feastDay' => 'October 4',
                'image' => '/images/saints/francis-assisi.jpg',
                'description' => 'Patron saint of animals and ecology',
                'bio' => 'Founder of the Franciscan Order, known for his love of nature and poverty.',
                'patronage' => 'Animals, Ecology, Merchants, Italy',
                'birthYear' => 1181,
                'deathYear' => 1226,
                'canonized' => 1228
            ],
            [
                'id' => 2,
                'name' => 'St. Therese of Lisieux',
                'feastDay' => 'October 1',
                'image' => '/images/saints/therese-lisieux.jpg',
                'description' => 'The Little Flower of Jesus',
                'bio' => 'Carmelite nun known for her "Little Way" of spiritual childhood.',
                'patronage' => 'Missionaries, Florists, Aviators, France',
                'birthYear' => 1873,
                'deathYear' => 1897,
                'canonized' => 1925
            ],
            [
                'id' => 3,
                'name' => 'St. Padre Pio',
                'feastDay' => 'September 23',
                'image' => '/images/saints/padre-pio.jpg',
                'description' => 'Mystic and stigmatist',
                'bio' => 'Capuchin friar who bore the stigmata and had the gift of bilocation.',
                'patronage' => 'Civil defense volunteers, Adolescents, Stress relief',
                'birthYear' => 1887,
                'deathYear' => 1968,
                'canonized' => 2002
            ],
            [
                'id' => 4,
                'name' => 'St. Mother Teresa',
                'feastDay' => 'September 5',
                'image' => '/images/saints/mother-teresa.jpg',
                'description' => 'Missionary of Charity',
                'bio' => 'Founder of the Missionaries of Charity, dedicated to serving the poorest of the poor.',
                'patronage' => 'World Youth Day, Missionaries of Charity, Calcutta',
                'birthYear' => 1910,
                'deathYear' => 1997,
                'canonized' => 2016
            ],
            [
                'id' => 5,
                'name' => 'St. John Paul II',
                'feastDay' => 'October 22',
                'image' => '/images/saints/john-paul-ii.jpg',
                'description' => 'The Great Pope',
                'bio' => 'Pope from 1978 to 2005, known for his extensive travels and role in ending communism.',
                'patronage' => 'World Youth Day, Families, Young people',
                'birthYear' => 1920,
                'deathYear' => 2005,
                'canonized' => 2014
            ],
            [
                'id' => 6,
                'name' => 'St. Teresa of Avila',
                'feastDay' => 'October 15',
                'image' => '/images/saints/teresa-avila.jpg',
                'description' => 'Doctor of the Church',
                'bio' => 'Carmelite nun, mystic, and reformer of the Carmelite Order.',
                'patronage' => 'Headache sufferers, Spanish Catholic Writers, Loss of parents',
                'birthYear' => 1515,
                'deathYear' => 1582,
                'canonized' => 1622
            ],
            [
                'id' => 7,
                'name' => 'St. Thomas Aquinas',
                'feastDay' => 'January 28',
                'image' => '/images/saints/thomas-aquinas.jpg',
                'description' => 'Doctor of the Church',
                'bio' => 'Dominican priest and theologian, author of Summa Theologica.',
                'patronage' => 'Academics, Students, Universities, Booksellers',
                'birthYear' => 1225,
                'deathYear' => 1274,
                'canonized' => 1323
            ],
            [
                'id' => 8,
                'name' => 'St. Catherine of Siena',
                'feastDay' => 'April 29',
                'image' => '/images/saints/catherine-siena.jpg',
                'description' => 'Doctor of the Church',
                'bio' => 'Dominican tertiary, mystic, and advisor to popes.',
                'patronage' => 'Italy, Fire prevention, Nurses, People ridiculed for their piety',
                'birthYear' => 1347,
                'deathYear' => 1380,
                'canonized' => 1461
            ],
            [
                'id' => 9,
                'name' => 'St. Ignatius of Loyola',
                'feastDay' => 'July 31',
                'image' => '/images/saints/ignatius-loyola.jpg',
                'description' => 'Founder of the Jesuits',
                'bio' => 'Founder of the Society of Jesus and author of the Spiritual Exercises.',
                'patronage' => 'Soldiers, Educators and education, Spiritual retreats',
                'birthYear' => 1491,
                'deathYear' => 1556,
                'canonized' => 1622
            ],
            [
                'id' => 10,
                'name' => 'St. Joan of Arc',
                'feastDay' => 'May 30',
                'image' => '/images/saints/joan-arc.jpg',
                'description' => 'Maid of Orleans',
                'bio' => 'French peasant girl who led the French army to victory during the Hundred Years\' War.',
                'patronage' => 'France, Soldiers, Captives, Military personnel',
                'birthYear' => 1412,
                'deathYear' => 1431,
                'canonized' => 1920
            ],
            [
                'id' => 11,
                'name' => 'St. Anthony of Padua',
                'feastDay' => 'June 13',
                'image' => '/images/saints/anthony-padua.jpg',
                'description' => 'Doctor of the Church',
                'bio' => 'Franciscan friar known for his powerful preaching and miracles.',
                'patronage' => 'Lost items, Lost people, Lost souls, Poor, Travelers',
                'birthYear' => 1195,
                'deathYear' => 1231,
                'canonized' => 1232
            ],
            [
                'id' => 12,
                'name' => 'St. Jude Thaddeus',
                'feastDay' => 'October 28',
                'image' => '/images/saints/jude-thaddeus.jpg',
                'description' => 'Patron of Lost Causes',
                'bio' => 'One of the Twelve Apostles, known for his intercession in seemingly hopeless cases.',
                'patronage' => 'Lost causes, Desperate situations, Hospitals',
                'birthYear' => null,
                'deathYear' => 65,
                'canonized' => null
            ]
        ];
        
        // If a specific saint is requested
        if ($request->has('id')) {
            $saintId = $request->get('id');
            $saint = collect($saints)->firstWhere('id', $saintId);
            
            if ($saint) {
                // Try to get external image for this saint
                $saint['image'] = $this->getSaintImage($saint['name']);
                return response()->json($saint);
            }
            
            return response()->json(['error' => 'Saint not found'], 404);
        }
        
        // Return all saints or a random selection
        if ($request->has('random') && $request->get('random')) {
            $count = $request->get('count', 5);
            $randomSaints = collect($saints)->shuffle()->take($count);
            
            // Add external images to random saints
            foreach ($randomSaints as &$saint) {
                $saint['image'] = $this->getSaintImage($saint['name']);
            }
            
            return response()->json($randomSaints);
        }
        
        return response()->json($saints);
    }
    
    /**
     * Get saint of the day
     */
    public function saintOfTheDay(Request $request): JsonResponse
    {
        $date = $request->get('date', now());
        $month = date('n', strtotime($date));
        $day = date('j', strtotime($date));
        
        // This would typically come from a database
        // For now, return a default saint
        $saintOfTheDay = [
            'id' => 1,
            'name' => 'St. Francis of Assisi',
            'feastDay' => 'October 4',
            'image' => $this->getSaintImage('St. Francis of Assisi'),
            'description' => 'Patron saint of animals and ecology',
            'bio' => 'Founder of the Franciscan Order, known for his love of nature and poverty.',
            'patronage' => 'Animals, Ecology, Merchants, Italy',
            'birthYear' => 1181,
            'deathYear' => 1226,
            'canonized' => 1228
        ];
        
        return response()->json($saintOfTheDay);
    }
    
    /**
     * Get saint image from external APIs with fallback
     */
    private function getSaintImage(string $saintName): string
    {
        $cacheKey = 'saint_image_' . md5($saintName);
        
        // Check cache first
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        // Try different image sources
        $imageUrl = $this->tryWikimediaCommons($saintName);
        
        if (!$imageUrl) {
            $imageUrl = $this->tryPixabay($saintName);
        }
        
        if (!$imageUrl) {
            $imageUrl = $this->tryUnsplash($saintName);
        }
        
        // Fallback to default image
        if (!$imageUrl) {
            $imageUrl = '/images/saints/default-saint.jpg';
        }
        
        // Cache the result for 24 hours
        Cache::put($cacheKey, $imageUrl, now()->addHours(24));
        
        return $imageUrl;
    }
    
    /**
     * Try to get image from Wikimedia Commons
     */
    private function tryWikimediaCommons(string $saintName): ?string
    {
        try {
            $query = urlencode($saintName . ' saint catholic');
            $url = "https://commons.wikimedia.org/w/api.php?action=query&list=search&srsearch={$query}&format=json&srnamespace=6&srlimit=5";
            
            $response = Http::timeout(5)->get($url);
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['query']['search']) && !empty($data['query']['search'])) {
                    $firstResult = $data['query']['search'][0];
                    $title = $firstResult['title'];
                    
                    // Get the actual image URL
                    $imageUrl = "https://commons.wikimedia.org/wiki/Special:FilePath/" . urlencode($title);
                    return $imageUrl;
                }
            }
        } catch (\Exception $e) {
            \Log::warning("Wikimedia Commons API failed for {$saintName}: " . $e->getMessage());
        }
        
        return null;
    }
    
    /**
     * Try to get image from Pixabay
     */
    private function tryPixabay(string $saintName): ?string
    {
        $apiKey = config('services.pixabay.key');
        
        if (!$apiKey) {
            return null;
        }
        
        try {
            $query = urlencode($saintName . ' saint');
            $url = "https://pixabay.com/api/?key={$apiKey}&q={$query}&image_type=photo&category=religion&safesearch=true&per_page=3";
            
            $response = Http::timeout(5)->get($url);
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['hits']) && !empty($data['hits'])) {
                    return $data['hits'][0]['webformatURL'];
                }
            }
        } catch (\Exception $e) {
            \Log::warning("Pixabay API failed for {$saintName}: " . $e->getMessage());
        }
        
        return null;
    }
    
    /**
     * Try to get image from Unsplash
     */
    private function tryUnsplash(string $saintName): ?string
    {
        $apiKey = config('services.unsplash.key');
        
        if (!$apiKey) {
            return null;
        }
        
        try {
            $query = urlencode($saintName . ' saint');
            $url = "https://api.unsplash.com/search/photos?query={$query}&per_page=3";
            
            $response = Http::withHeaders([
                'Authorization' => 'Client-ID ' . $apiKey
            ])->timeout(5)->get($url);
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['results']) && !empty($data['results'])) {
                    return $data['results'][0]['urls']['regular'];
                }
            }
        } catch (\Exception $e) {
            \Log::warning("Unsplash API failed for {$saintName}: " . $e->getMessage());
        }
        
        return null;
    }
    
    /**
     * Get saint image by name (public endpoint)
     */
    public function getSaintImageByName(Request $request): JsonResponse
    {
        $saintName = $request->get('name');
        
        if (!$saintName) {
            return response()->json(['error' => 'Saint name is required'], 400);
        }
        
        $imageUrl = $this->getSaintImage($saintName);
        
        return response()->json([
            'saint_name' => $saintName,
            'image_url' => $imageUrl,
            'source' => 'external_api'
        ]);
    }
} 