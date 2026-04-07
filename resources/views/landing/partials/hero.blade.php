<section class="landing-hero">
    <div class="landing-container landing-hero-grid">
        <div class="landing-hero-copy animate__animated animate__fadeInUp">
            <div class="landing-kicker">DUKAFY</div>
            <h1 class="landing-hero-title">Rahisisha uendeshaji wa biashara yako</h1>
            <p class="landing-hero-subtitle">Dukafy ni mfumo wa mauzo (POS), bidhaa, wateja, na ripoti — umeundwa kwa biashara ndogo na zinazokua.</p>

            <div class="landing-hero-actions">
                @if (Route::has('login'))
                    <a class="landing-btn" href="{{ route('login') }}">Ingia</a>
                @endif
                <a class="landing-btn landing-btn-ghost" href="{{ route('about') }}">Jifunze zaidi</a>
            </div>
        </div>
    </div>
</section>
