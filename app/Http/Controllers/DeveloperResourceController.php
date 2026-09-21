<?php

namespace App\Http\Controllers;

use App\Enums\DeveloperResourcePricing;
use App\Enums\DeveloperResourceStatus;
use App\Models\DeveloperResource;
use App\Models\DeveloperResourceCategory;
use App\Models\DeveloperResourceTechnology;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DeveloperResourceController extends Controller
{
    public function index(Request $request): View
    {
        $categorySlug = $request->string('categoria')->toString();
        $technologySlug = $request->string('tecnologia')->toString();
        $pricing = $request->string('modalidad')->toString();
        $search = $request->string('q')->trim()->toString();
        $withoutCard = $request->boolean('sin_tarjeta');

        $query = DeveloperResource::published()
            ->with(['category', 'technologies'])
            ->orderByDesc('is_featured')
            ->orderByDesc('last_verified_at');

        if ($categorySlug !== '') {
            $query->whereHas('category', fn ($query) => $query->where('slug', $categorySlug));
        }

        if ($technologySlug !== '') {
            $query->whereHas('technologies', fn ($query) => $query->where('slug', $technologySlug));
        }

        if (in_array($pricing, array_column(DeveloperResourcePricing::cases(), 'value'), true)) {
            $query->where('pricing', $pricing);
        }

        if ($withoutCard) {
            $query->where('no_card_required', true);
        }

        if ($search !== '') {
            $query->where(function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        return view('recursos.index', [
            'resources' => $query->paginate(12)->withQueryString(),
            'featured' => DeveloperResource::published()->with(['category', 'technologies'])
                ->where('is_featured', true)->latest('last_verified_at')->limit(3)->get(),
            'categories' => DeveloperResourceCategory::orderBy('sort_order')->get(),
            'technologies' => DeveloperResourceTechnology::whereHas('resources', fn ($query) => $query->published())
                ->orderBy('name')->get(),
            'filters' => compact('categorySlug', 'technologySlug', 'pricing', 'search', 'withoutCard'),
        ]);
    }

    public function show(DeveloperResource $developerResource): View
    {
        abort_unless(
            $developerResource->status === DeveloperResourceStatus::Published && $developerResource->published_at !== null,
            404,
        );

        $developerResource->load(['category', 'technologies', 'author']);

        return view('recursos.show', [
            'resource' => $developerResource,
            'structuredData' => json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'SoftwareApplication',
                'name' => $developerResource->name,
                'url' => $developerResource->external_url,
                'applicationCategory' => $developerResource->category->name,
                'operatingSystem' => 'Web',
            ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT),
        ]);
    }
}
