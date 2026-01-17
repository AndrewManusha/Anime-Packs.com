<?php
use Carbon\Carbon;

use App\Models\PageAnalytics;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request; // Use this only

function getTime($publicationDateTime)
{
    $publicationDate = Carbon::parse($publicationDateTime);
    $currentDate = Carbon::now();

    if ($currentDate->lessThan($publicationDate)) {
        return $publicationDate->diffForHumans($currentDate, ['parts' => 2, 'join' => ' and ']) . ' left until publication';
    }
    
    return $publicationDate->diffForHumans();
}

function shortenNumber($number) {
    $suffixes = ['','K','M','B','T']; // Суффиксы для 10^3, 10^6, 10^9 и т.д.
    $suffixIndex = 0;

    while ($number >= 1000 && $suffixIndex < count($suffixes) - 1) {
        $number /= 1000;
        $suffixIndex++;
    }

    // Если число целое, то не показывать десятичную точку
    return ($number == floor($number) ? round($number) : round($number, 1)) . $suffixes[$suffixIndex];
}

function renderStars($rating, $clickable = false) {
    $fullStars = floor($rating);
    $hasHalfStar = ($rating - $fullStars) >= 0.5;
    $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
    $style = 'style="cursor:pointer;"';

    $starsHtml = '';

    for ($i = 1; $i <= $fullStars; $i++) {
        $starsHtml .= '<div class="star full" ' . ($clickable ? $style . 'onclick="sendRating(' . $i . ')"' : '') . '></div>';
    }

    if ($hasHalfStar) {
        $starsHtml .= '<div class="star half" ' . ($clickable ? $style . 'onclick="sendRating(' . ($fullStars + 1) . ')"' : '') . '></div>';
    }

    for ($i = $fullStars + $hasHalfStar + 1; $i <= 5; $i++) {
        $starsHtml .= '<div class="star empty" ' . ($clickable ? $style . 'onclick="sendRating(' . $i . ')"' : '') . '></div>';
    }

    return $starsHtml;
}

?>