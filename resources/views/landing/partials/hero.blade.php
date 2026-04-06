<section class="landing-hero">
    <div class="landing-container landing-hero-grid">
        <div class="landing-hero-copy animate__animated animate__fadeInUp">
            <div class="landing-kicker">ABOUT US – MALKIA</div>
            <h1 class="landing-hero-title">Supporting women through the journey of motherhood</h1>
            <p class="landing-hero-subtitle">At Malkia Konnect, we are dedicated to supporting women through one of the most beautiful and transformative journeys of their lives — motherhood.</p>

            <div class="landing-hero-actions">
                @if (Route::has('register'))
                    <a class="landing-btn" href="{{ route('register') }}">Join Konnect</a>
                @elseif (Route::has('login'))
                    <a class="landing-btn" href="{{ route('login') }}">Join Konnect</a>
                @endif
                <a class="landing-btn landing-btn-ghost" href="#">Malkia Shop</a>
            </div>
        </div>
    </div>
</section>
