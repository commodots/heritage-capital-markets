<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import SeoHead from '@/Components/SeoHead.vue'
import teamImage from '@/assets/about/heritage-team.jpg'
import receptionImage from '@/assets/about/heritage-reception.jpg'
</script>

<template>
    <PublicLayout>
        <SeoHead
            title="Board & Management"
            description="Learn about the people and leadership behind Heritage Capital Markets and explore career opportunities."
            canonical-path="/board-management"
        />

        <section class="bg-heritage-deep py-24 text-white">
            <div class="container-heritage">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-heritage-gold">Our People</p>
                <h1 class="mt-4 max-w-3xl font-display text-4xl font-semibold leading-tight md:text-6xl">
                    Board &amp; Management
                </h1>
                <p class="mt-6 max-w-2xl text-base leading-7 text-white/75">
                    The people of Heritage bring together experience, market knowledge and a commitment to serving our clients.
                </p>
            </div>
        </section>

        <section class="bg-white py-16 md:py-24">
            <div class="container-heritage grid items-center gap-10 lg:grid-cols-2">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-heritage-gold">Leadership</p>
                    <h2 class="mt-3 font-display text-3xl font-semibold text-heritage-navy md:text-4xl">
                        Experience guided by integrity.
                    </h2>
                    <p class="mt-5 leading-7 text-heritage-muted">
                        Our leadership is committed to responsible oversight, professional standards and a client-focused approach.
                        We bring together people with the expertise to help clients navigate the capital markets.
                    </p>
                    <p class="mt-4 leading-7 text-heritage-muted">
                        Information about individual board and management members will be published here as approved profiles become available.
                    </p>
                </div>
                <img :src="teamImage" alt="Heritage Capital Markets team" class="h-72 w-full rounded-md object-cover md:h-96" />
            </div>
        </section>

        <section id="careers" class="scroll-mt-24 bg-heritage-offwhite py-16 md:py-24">
            <div class="container-heritage grid items-center gap-10 lg:grid-cols-2">
                <div class="order-2 overflow-hidden rounded-md lg:order-1">
                    <img :src="receptionImage" alt="Heritage Capital Markets office reception" class="h-72 w-full object-cover md:h-96" />
                </div>
                <div class="order-1 lg:order-2">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-heritage-gold">Careers</p>
                    <h2 class="mt-3 font-display text-3xl font-semibold text-heritage-navy md:text-4xl">
                        Build your future with Heritage.
                    </h2>
                    <p class="mt-5 leading-7 text-heritage-muted">
                        We value people who bring curiosity, professionalism and a commitment to excellent client service.
                        Contact our team to enquire about career opportunities.
                    </p>
                    <a href="/contact-us" class="mt-7 inline-flex items-center gap-2 rounded bg-heritage-gold px-6 py-3 font-semibold text-heritage-deep transition hover:bg-heritage-gold-light">
                        Contact Us <span aria-hidden="true">→</span>
                    </a>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
