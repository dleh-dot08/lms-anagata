@extends('layouts.template')

@section('content')
<div class="container py-5">
    <h2 class="mt-4 mb-4 text-center fw-bold">Frequently Asked Questions</h2>

    {{-- 🔍 Search Bar --}}
    <form method="GET" action="{{ url('/faq') }}" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Cari pertanyaan..."
                value="{{ request('search') }}">
                <button type="submit" class="btn btn-warning text-white fw-semibold"
                    style="border-top-right-radius: 2rem; border-bottom-right-radius: 2rem;">
                    Cari
                </button>
        </div>
    </form>


    {{-- 🔽 Accordion FAQs --}}
    <div class="accordion" id="faqAccordion">
            @forelse($faqs as $faq)
                <div class="accordion-item mb-3 border-0 shadow-sm rounded" style="background-color: rgb(105 108 255 / 16%);">
                    <h2 class="accordion-header" id="heading{{ $faq->id }}">
                        <button class="accordion-button collapsed fw-semibold bg-transparent" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapse{{ $faq->id }}"
                            aria-expanded="false" aria-controls="collapse{{ $faq->id }}">
                            {{ $faq->question }}
                        </button>
                    </h2>
                    <div id="collapse{{ $faq->id }}" class="accordion-collapse collapse"
                        aria-labelledby="heading{{ $faq->id }}" data-bs-parent="#faqAccordion">
                        <div class="accordion-body bg-white rounded-bottom text-break overflow-auto">
                            {!! $faq->answer !!}
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-info text-center">
                    Tidak ada FAQ tersedia saat ini.
                </div>
            @endforelse
        </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $faqs->withQueryString()->links() }}
    </div>
</div>
@endsection
