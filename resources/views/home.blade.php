@extends('layouts.app')

@section('title', 'Anime Packs - The Best Anime-Themed Minecraft Packs' )

@section('canonical', route('home', [], true))

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link rel="stylesheet" href="{{ mix('css/home.css') }}">
@endsection

@section('robots', 'index, follow')

@section('meta-description', 'Discover high-quality 3D anime-themed Minecraft resource packs. Only on Anime Packs — handcrafted models that bring your favorite anime into the game.')

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
<div class="layout">
    <div class="slogan">
        <h1>Your <span class="highlight-anime">Ultimate</span> Source for <span class="highlight-anime">Anime</span>-Themed Minecraft <span class="highlight-anime">Packs</span>!</h1>
        <p>Start building your anime-themed Minecraft world today.</p>
    </div>
    
    <div class="popular">
        <div>
            <h2>Popular</h2>
        </div>
        @include('partials.PopularInMonth', ['topPacks' => $topPacks])
    </div>
    
    <div id="about-anime-packs">
        <h2>About Anime Packs</h2>
    
        <p><strong>Anime Packs</strong> is a fan-made project created by a fan, for fans. Here you will find unique 3D resource packs inspired by popular and iconic anime, bringing signature weapons, artifacts, and accessories into the world of Minecraft. Each pack is not just a decorative addition but a carefully crafted and refined work made entirely from scratch by hand.</p>
    
        <p>Unlike many projects that use borrowed assets or reworked models, all resource packs here are created personally by me. I don’t just replicate the shape of an item — I take into account every edge, curve, seam, and texture to achieve maximum accuracy while preserving Minecraft’s aesthetic. My goal is not to copy but to recreate: to capture the spirit of the original and adapt it seamlessly into Minecraft’s blocky style.</p>
    
        <p>My resource packs work on <strong>Minecraft 1.20.1</strong> — this is <strong>not the newest version</strong>, but the <strong>latest supported version</strong> for which a stable release of <strong>CIT Resewn</strong> is available. On newer versions of Minecraft, the packs currently do not function because CIT Resewn has not yet been updated for them. The packs should also work correctly on older versions down to <strong>1.16</strong>, but not earlier, due to changes in the game’s internal architecture.<br>
        For full functionality, you need to install <strong>CIT Resewn</strong> — a mod designed for the <strong>Fabric</strong> loader that allows custom items to be displayed based on their name, state, or other parameters. Without it, the models will not appear in the game. This is the <strong>recommended installation method</strong>, and all my testing is done using this setup.<br>
        If you prefer to use <strong>OptiFine</strong>, you may try it, but I <strong>cannot guarantee compatibility</strong> since I do not officially support it and do not test the packs with OptiFine.</p>
    
        <p>This project is developed solo, and each pack is the result of hours, sometimes days, of handcrafting. I have invested not only skills but also inspiration gathered from years of watching anime (currently over 100 titles). Thanks to this approach, each model feels alive and recognizable — not like a cheap imitation but as a full-fledged part of the Minecraft world infused with the atmosphere of the original work.</p>
    
        <h3>You can use these resource packs for:</h3>
        <ul>
            <li>Roleplaying as your favorite characters;</li>
            <li>Creating atmospheric visual builds;</li>
            <li>Recording videos and streaming;</li>
            <li>Decorating personal worlds with anime aesthetics;</li>
            <li>Finding inspiration for your own Minecraft stories.</li>
        </ul>
    
        <p>In the future, I plan to add more content: armor, decorative blocks, custom animations, and even full mechanics through mods and plugins — all with the same attention to quality and style.</p>
    
        <p><strong>⚠️ Important</strong>: Using these packs in public modpacks, servers, or commercial projects <strong>requires my written permission</strong>. This is necessary to protect copyrights and control distribution. Read more in the <a href="/terms-of-use">Terms of Use</a>.</p>
    
        <p>If you need a <strong>custom pack</strong>, I also take commissions. Visit the <a href="/commission">Commission</a> page to learn details and send your request. All custom orders are created with the same quality and care as the main collection.</p>
    
        <p><strong>Anime Packs</strong> is where pixel art aesthetics meet anime inspiration. If you’re looking for more than just textures — you’ve come to the right place.</p>
    </div>

</div>
@endsection

@section('scripts')

@stack('scripts')

@endsection