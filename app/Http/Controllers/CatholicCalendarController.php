<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class CatholicCalendarController extends Controller
{
    /**
     * Get Catholic calendar information for the current date
     */
    public function index(Request $request): JsonResponse
    {
        $date = $request->get('date', now());
        $carbonDate = Carbon::parse($date);
        
        // Try to fetch from external API first
        $externalData = $this->fetchExternalCalendarData($carbonDate);
        
        if ($externalData) {
            return response()->json($externalData);
        }
        
        // Fallback to internal calculation if external API fails
        $calendarData = [
            'date' => $carbonDate->format('Y-m-d'),
            'liturgicalSeason' => $this->getLiturgicalSeason($carbonDate),
            'feastDay' => $this->getFeastDay($carbonDate),
            'saintOfTheDay' => $this->getSaintOfTheDay($carbonDate),
            'color' => $this->getLiturgicalColor($carbonDate),
            'reading' => $this->getDailyReading($carbonDate),
            'weekday' => $carbonDate->format('l'),
            'seasonWeek' => null,
            'celebrations' => []
        ];
        
        return response()->json($calendarData);
    }
    
    /**
     * Fetch calendar data from external API
     */
    private function fetchExternalCalendarData(Carbon $date): ?array
    {
        try {
            $dateString = $date->format('Y-m-d');
            $response = Http::timeout(5)->get("http://calapi.inadiutorium.cz/api/v0/en/calendars/general-en/{$dateString}");
            
            if ($response->successful()) {
                $data = $response->json();
                
                // Transform external API data to our format
                return [
                    'date' => $data['date'],
                    'liturgicalSeason' => $this->formatSeason($data['season']),
                    'feastDay' => $this->getFeastDayFromCelebrations($data['celebrations']),
                    'saintOfTheDay' => $this->getSaintFromCelebrations($data['celebrations']),
                    'color' => $this->getColorFromCelebrations($data['celebrations']),
                    'reading' => $this->getDailyReading($date),
                    'weekday' => ucfirst($data['weekday']),
                    'seasonWeek' => $data['season_week'] ?? null,
                    'celebrations' => $data['celebrations'] ?? [],
                    'source' => 'external_api'
                ];
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to fetch external calendar data: ' . $e->getMessage());
        }
        
        return null;
    }
    
    /**
     * Format season from external API
     */
    private function formatSeason(string $season): string
    {
        $seasonMap = [
            'advent' => 'Advent',
            'christmas' => 'Christmas Season',
            'lent' => 'Lent',
            'easter' => 'Easter Season',
            'ordinary' => 'Ordinary Time'
        ];
        
        return $seasonMap[$season] ?? ucfirst($season);
    }
    
    /**
     * Get feast day from celebrations array
     */
    private function getFeastDayFromCelebrations(array $celebrations): string
    {
        if (empty($celebrations)) {
            return 'No special feast today';
        }
        
        // Get the highest rank celebration (lowest rank_num)
        $highestRank = collect($celebrations)->sortBy('rank_num')->first();
        
        return $highestRank['title'] ?? 'No special feast today';
    }
    
    /**
     * Get saint from celebrations array
     */
    private function getSaintFromCelebrations(array $celebrations): string
    {
        if (empty($celebrations)) {
            return 'No saint feast today';
        }
        
        // Look for saint celebrations
        foreach ($celebrations as $celebration) {
            $title = $celebration['title'] ?? '';
            if (stripos($title, 'saint') !== false || stripos($title, 'st.') !== false) {
                return $title;
            }
        }
        
        // If no saint found, return the first celebration
        return $celebrations[0]['title'] ?? 'No saint feast today';
    }
    
    /**
     * Get liturgical color from celebrations array
     */
    private function getColorFromCelebrations(array $celebrations): string
    {
        if (empty($celebrations)) {
            return 'Green'; // Default for Ordinary Time
        }
        
        // Get color from the highest rank celebration
        $highestRank = collect($celebrations)->sortBy('rank_num')->first();
        
        $colorMap = [
            'white' => 'White',
            'red' => 'Red',
            'green' => 'Green',
            'purple' => 'Purple',
            'pink' => 'Pink',
            'gold' => 'Gold'
        ];
        
        $color = $highestRank['colour'] ?? 'green';
        return $colorMap[$color] ?? ucfirst($color);
    }
    
    /**
     * Get liturgical season for the given date (fallback method)
     */
    private function getLiturgicalSeason(Carbon $date): string
    {
        $month = $date->month;
        $day = $date->day;
        
        // Advent (4 weeks before Christmas)
        $christmas = Carbon::create($date->year, 12, 25);
        $adventStart = $christmas->copy()->subWeeks(4);
        if ($date->between($adventStart, $christmas->copy()->subDay())) {
            return 'Advent';
        }
        
        // Christmas Season (Dec 25 - Jan 6)
        if ($month === 12 && $day >= 25) {
            return 'Christmas Season';
        }
        if ($month === 1 && $day <= 6) {
            return 'Christmas Season';
        }
        
        // Lent (Ash Wednesday to Holy Thursday)
        $easter = $this->getEasterDate($date->year);
        $ashWednesday = $easter->copy()->subDays(46);
        $holyThursday = $easter->copy()->subDays(3);
        if ($date->between($ashWednesday, $holyThursday)) {
            return 'Lent';
        }
        
        // Easter Triduum (Holy Thursday to Easter Sunday)
        $easterSunday = $easter->copy()->addDays(1);
        if ($date->between($holyThursday, $easterSunday)) {
            return 'Easter Triduum';
        }
        
        // Easter Season (Easter Sunday to Pentecost)
        $pentecost = $easter->copy()->addDays(49);
        if ($date->between($easterSunday, $pentecost)) {
            return 'Easter Season';
        }
        
        // Ordinary Time
        return 'Ordinary Time';
    }
    
    /**
     * Get feast day for the given date (fallback method)
     */
    private function getFeastDay(Carbon $date): string
    {
        $month = $date->month;
        $day = $date->day;
        
        $feasts = [
            '1-1' => 'Solemnity of Mary, Mother of God',
            '1-6' => 'Epiphany of the Lord',
            '2-2' => 'Presentation of the Lord',
            '3-19' => 'Solemnity of Saint Joseph',
            '3-25' => 'Annunciation of the Lord',
            '6-24' => 'Nativity of Saint John the Baptist',
            '6-29' => 'Saints Peter and Paul',
            '8-15' => 'Assumption of the Blessed Virgin Mary',
            '11-1' => 'All Saints Day',
            '11-2' => 'All Souls Day',
            '12-8' => 'Immaculate Conception',
            '12-25' => 'Christmas',
        ];
        
        $key = $month . '-' . $day;
        return $feasts[$key] ?? 'No special feast today';
    }
    
    /**
     * Get saint of the day for the given date (fallback method)
     */
    private function getSaintOfTheDay(Carbon $date): string
    {
        $month = $date->month;
        $day = $date->day;
        
        $saints = [
            '1-1' => 'St. Mary, Mother of God',
            '1-2' => 'St. Basil the Great',
            '1-3' => 'St. Genevieve',
            '1-4' => 'St. Elizabeth Ann Seton',
            '1-5' => 'St. John Neumann',
            '1-6' => 'St. André Bessette',
            '1-7' => 'St. Raymond of Peñafort',
            '1-8' => 'St. Thérèse of Lisieux',
            '1-9' => 'St. Adrian of Canterbury',
            '1-10' => 'St. Gregory of Nyssa',
            '1-11' => 'St. Theodosius the Cenobiarch',
            '1-12' => 'St. Marguerite Bourgeoys',
            '1-13' => 'St. Hilary of Poitiers',
            '1-14' => 'St. Felix of Nola',
            '1-15' => 'St. Paul the Hermit',
            '1-16' => 'St. Berard and Companions',
            '1-17' => 'St. Anthony of Egypt',
            '1-18' => 'St. Charles of Sezze',
            '1-19' => 'St. Fabian',
            '1-20' => 'St. Sebastian',
            '1-21' => 'St. Agnes',
            '1-22' => 'St. Vincent of Saragossa',
            '1-23' => 'St. Ildephonsus',
            '1-24' => 'St. Francis de Sales',
            '1-25' => 'Conversion of St. Paul',
            '1-26' => 'St. Timothy and St. Titus',
            '1-27' => 'St. Angela Merici',
            '1-28' => 'St. Thomas Aquinas',
            '1-29' => 'St. Gildas the Wise',
            '1-30' => 'St. Martina',
            '1-31' => 'St. John Bosco',
            '2-1' => 'St. Brigid of Ireland',
            '2-2' => 'Presentation of the Lord',
            '2-3' => 'St. Blaise',
            '2-4' => 'St. Jane of Valois',
            '2-5' => 'St. Agatha',
            '2-6' => 'St. Paul Miki and Companions',
            '2-7' => 'St. Colette',
            '2-8' => 'St. Josephine Bakhita',
            '2-9' => 'St. Apollonia',
            '2-10' => 'St. Scholastica',
            '2-11' => 'Our Lady of Lourdes',
            '2-12' => 'St. Julian the Hospitaller',
            '2-13' => 'St. Catherine de Ricci',
            '2-14' => 'St. Valentine',
            '2-15' => 'St. Claude de la Colombière',
            '2-16' => 'St. Gilbert of Sempringham',
            '2-17' => 'St. Seven Founders of the Servite Order',
            '2-18' => 'St. Simeon',
            '2-19' => 'St. Conrad of Piacenza',
            '2-20' => 'St. Jacinta Marto',
            '2-21' => 'St. Peter Damian',
            '2-22' => 'Chair of St. Peter',
            '2-23' => 'St. Polycarp',
            '2-24' => 'St. Matthias',
            '2-25' => 'St. Tarasius',
            '2-26' => 'St. Porphyry of Gaza',
            '2-27' => 'St. Gabriel of Our Lady of Sorrows',
            '2-28' => 'St. Hilary of Poitiers',
            '2-29' => 'St. Oswald of Worcester',
        ];
        
        $key = $month . '-' . $day;
        return $saints[$key] ?? 'No saint feast today';
    }
    
    /**
     * Get liturgical color for the given date (fallback method)
     */
    private function getLiturgicalColor(Carbon $date): string
    {
        $season = $this->getLiturgicalSeason($date);
        
        switch ($season) {
            case 'Advent':
                return 'Purple';
            case 'Christmas Season':
                return 'White';
            case 'Lent':
                return 'Purple';
            case 'Easter Triduum':
                return 'Red';
            case 'Easter Season':
                return 'White';
            default:
                return 'Green';
        }
    }
    
    /**
     * Get daily reading reference
     */
    private function getDailyReading(Carbon $date): string
    {
        // This would typically connect to a lectionary database
        // For now, return a simple reference
        return 'Daily Mass Readings - ' . $date->format('F j, Y');
    }
    
    /**
     * Calculate Easter date using Meeus/Jones/Butcher algorithm
     */
    private function getEasterDate(int $year): Carbon
    {
        $a = $year % 19;
        $b = floor($year / 100);
        $c = $year % 100;
        $d = floor($b / 4);
        $e = $b % 4;
        $f = floor(($b + 8) / 25);
        $g = floor(($b - $f + 1) / 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = floor($c / 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = floor(($a + 11 * $h + 22 * $l) / 451);
        $month = floor(($h + $l - 7 * $m + 114) / 31);
        $day = (($h + $l - 7 * $m + 114) % 31) + 1;
        
        return Carbon::create($year, $month, $day);
    }
} 