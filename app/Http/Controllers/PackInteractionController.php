<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use App\Models\PageAnalytics;
use App\Models\Rate;
use App\Models\User;

class PackInteractionController extends Controller
{
    // === Просмотр пака ===
    public function view(Request $request)
    {
        $this->logAction($request->user_id, $request->page_url, 'view');
    }

    // === Скачивание пака ===
    public function download(Request $request)
    {
        $pageUrl = $this->normalizeUrl($request->page_url);

        foreach (['view', 'download'] as $action) {
            $this->logAction($request->user_id, $pageUrl, $action);
        }

        return Response::download(public_path($request->file_url));
    }

    // === Оценка пака ===
    public function rate(Request $request)
    {
        $validated = $request->validate([
            'user_id'  => 'required|string|max:255',
            'page_url' => 'required|string|max:2048',
            'rating'   => 'required|integer|min:1|max:5',
        ]);

        // Проверка авторизации
        if (!User::where('google_id', $validated['user_id'])->exists()) {
            return response()->json(['error' => 'Доступ запрещён'], 403);
        }

        $lastRate = Rate::where('google_id', $validated['user_id'])
                        ->where('page_url', $validated['page_url'])
                        ->latest('updated_at')
                        ->first();

        $newRating = $validated['rating'];
        $difference = $lastRate ? $newRating - $lastRate->rate : $newRating;
        $isNew = $lastRate ? 0 : 1;

        if ($difference === 0) {
            return response()->json(['error' => 'Вы уже оставили свою оценку'], 409);
        }

        Rate::create([
            'google_id' => $validated['user_id'],
            'page_url'  => $validated['page_url'],
            'rate'      => $newRating,
            'difference'=> $difference,
            'is_new'    => $isNew,
        ]);

        return response()->json([
            'message'    => 'Оценка сохранена',
            'score'      => $newRating,
            'difference' => $difference,
            'is_new'     => $isNew,
        ]);
    }

    // === Вспомогательные методы ===

    private function normalizeUrl(string $url): string
    {
        return Str::replaceLast('/download', '', Str::replaceFirst('https://anime-packs.com', '', $url));
    }

    private function logAction(?string $userId, ?string $url, string $action): void
    {
        PageAnalytics::firstOrCreate(
            [
                'user_identifier' => $userId,
                'page_url'        => $this->normalizeUrl($url),
                'action'          => $action,
                'action_date'     => Carbon::today()->toDateString(),
            ],
            [
                'time' => Carbon::now()->toTimeString(),
            ]
        );
    }
}
