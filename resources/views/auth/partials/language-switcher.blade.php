<div class="d-flex justify-content-end mb-3">
    <a href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL('en', null, [], true) }}"
        class="btn btn-sm {{ app()->isLocale('en') ? 'btn-primary' : 'btn-outline-primary' }} mx-1">
        {{ __('common.language.english') }}
    </a>
    <a href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL('ar', null, [], true) }}"
        class="btn btn-sm {{ app()->isLocale('ar') ? 'btn-primary' : 'btn-outline-primary' }} mx-1">
        {{ __('common.language.arabic') }}
    </a>
</div>
