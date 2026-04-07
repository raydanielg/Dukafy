<section class="landing-section landing-commitment">
    <div class="landing-container landing-commitment-inner animate__animated animate__fadeInUp">
        <div class="landing-chip">OUR COMMITMENT</div>
        <h2 class="landing-h2">Dukafy inasimama na biashara yako</h2>
        <p class="landing-p">Tumejikita kuhakikisha mauzo yako, stock, na ripoti vinakuwa wazi na vinapatikana kwa urahisi.</p>
        <p class="landing-p">Unapata mfumo unaoeleweka kwa muuzaji na unaompa mmiliki picha halisi ya biashara.</p>

        <div class="landing-testimonials" data-testimonials>
            <div class="landing-testimonials-track" data-testimonials-track>
                @forelse(($testimonials ?? collect()) as $t)
                    <div class="landing-testimonial">
                        <div class="landing-testimonial-quote">“{{ $t->quote }}”</div>
                        <div class="landing-testimonial-meta">
                            <span class="landing-testimonial-name">{{ $t->name }}</span>
                            @if (!empty($t->role) || !empty($t->location))
                                <span class="landing-testimonial-sep">·</span>
                                <span class="landing-testimonial-sub">{{ trim(($t->role ?? '') . ' ' . (!empty($t->location) ? ('— ' . $t->location) : '')) }}</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="landing-testimonial">
                        <div class="landing-testimonial-quote">“Dukafy imenisaidia kujua mauzo ya leo, bidhaa zinazoisha, na faida kwa haraka bila makosa.”</div>
                        <div class="landing-testimonial-meta">
                            <span class="landing-testimonial-name">Amina S.</span>
                            <span class="landing-testimonial-sep">·</span>
                            <span class="landing-testimonial-sub">Mmiliki wa Duka — Dar es Salaam</span>
                        </div>
                    </div>
                @endforelse
            </div>
            <div class="landing-testimonials-dots" data-testimonials-dots></div>
        </div>
    </div>
</section>
