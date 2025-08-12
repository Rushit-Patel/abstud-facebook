@forelse($leads as $lead)
    <div class="kt-menu kt-menu-default px-0.5 flex-col">
        <div class="grid gap-1">
            <div class="kt-menu-item">
                <div class="kt-menu-link flex justify-between gap-2">
                    <div class="flex items-center gap-2.5">
                        <div class="flex flex-col">
                            <a class="text-sm font-semibold text-mono hover:text-primary mb-px" href="{{ route('team.client.show', $lead->id) }}">
                                {{ $lead?->client?->first_name }} {{ $lead?->client?->last_name }}
                            </a>
                            <span class="text-2sm font-normal text-muted-foreground">
                                {{ $lead?->client?->email_id }}
                            </span>
                            <span class="text-2sm font-normal text-muted-foreground">
                                {{ (isset($lead?->client?->country_code) && substr($lead->client->country_code, 0, 1) === '+') ? $lead->client->country_code : '+' . $lead->client->country_code }} {{ $lead?->client?->mobile_no }}
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <div class="kt-badge rounded-full kt-badge-outline
                            {{ $lead->getStatus->name == 'Close' ? 'kt-badge-destructive' : 'kt-badge-primary' }} gap-1.5">
                            <span class="kt-badge-dot"></span>
                            {{ strtoupper($lead?->getStatus?->name) }}
                        </div>

                        <div class="kt-badge rounded-full kt-badge-outline
                            {{ $lead->getStatus->name == 'Close' ? 'kt-badge-destructive' : 'kt-badge-secondary' }} gap-1.5">
                            <span class="kt-badge-dot"></span>
                            {{ strtoupper($lead?->getSubStatus?->name) }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="p-4 text-muted-foreground">No results found.</div>
@endforelse
