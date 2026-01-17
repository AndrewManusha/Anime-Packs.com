@extends('layouts.app')

@section('title', ucwords(str_replace('-', ' ', $item->franchise . ': ' . $item->title . ' ' . $item->type . ' for minecraft ')))

@section('canonical', route('pack', [
    'section' => $item->type,
    'franchise' => $item->franchise,
    'name' => ($name = str_replace('_', '-', pathinfo($item->file, PATHINFO_FILENAME))) === $item->franchise ? null : $name
], true))

@section('styles')
    <link rel="stylesheet" href="{{ mix('css/download.css') }}">
@endsection

@section('robots', 'noindex, follow')

@section('ad')
<div class="side-container left desktop-only">
    <div class="ad-inner">
        
    </div>
</div>

<div class="side-container right desktop-only">
    <div class="ad-inner">
        
    </div>
</div>
@endsection

@section('content')
<div class="content-ad">
    
</div>

<div class="download-timer">
    <p id="countdown">will start downloading in 5 seconds...</p>
    <p id="retryMessage" style="display:none;">If your download didn’t start, <a id="download" href="">try again</a></p>
</div>

<div class="title">
    <h1>Download {{ $item->title }}</h1>
</div>

<div class="info-panel">
    
</div>

<div class="description">
    <p>{!! $item->description !!}</p>
</div>
@endsection

@section('scripts')
<script>
    window.onload = function() {
        const fileUrl = @json('storage' . $fileUrl . '/' . $item->file);
        const countdownElement = document.getElementById('countdown');
        const retryMessage = document.getElementById('retryMessage');
        let countdown = 4;
        
        let userId = localStorage.getItem('user_id');

        if (!userId) {
            userId = [...crypto.getRandomValues(new Uint8Array(8))].map(byte => byte.toString(16).padStart(2, '0')).join('');
            localStorage.setItem('user_id', userId);
        }
        
        const serverUserId = @json($userId);
        if (serverUserId && serverUserId !== userId) {
            userId = serverUserId;
            localStorage.setItem('user_id', userId);
        }
        
        document.getElementById('download').addEventListener('click', function(event) {
            event.preventDefault();
            sendDownloadRequest();
        });

        function sendDownloadRequest() {
            fetch('/api/download', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    page_url: window.location.href,
                    user_id: userId,
                    file_url: fileUrl
                })
            })
            .then(response => {
                if (response.ok) {
                    return response.blob();
                }
                throw new Error('Network response was not ok');
            })
            .then(blob => {
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = '{{$item->file}}';
                link.click();
            })
            .catch(error => {
                console.error('There was a problem with the fetch operation:', error);
            });
        }
        
        // Функция отсчета времени
        const countdownTimer = setInterval(() => {
            countdownElement.textContent = `will start downloading in ${countdown} seconds...`;
            if (--countdown < 0) {
                clearInterval(countdownTimer);
                sendDownloadRequest();
                countdownElement.style.display = 'none';
                retryMessage.style.display = 'block';
            }
        }, 1300);
    };
</script>
@endsection