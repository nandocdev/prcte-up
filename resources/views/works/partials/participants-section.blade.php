@php
    $rawParticipants = old('participants');

    if (is_null($rawParticipants)) {
        $rawParticipants = $participants ?? [];
    }

    if ($rawParticipants instanceof \Illuminate\Database\Eloquent\Collection) {
        $rawParticipants = $rawParticipants->all();
    }

    if ($rawParticipants instanceof \Illuminate\Support\Collection) {
        $rawParticipants = $rawParticipants->all();
    }

    if (!is_array($rawParticipants)) {
        $rawParticipants = [];
    }

    $normalizedParticipants = collect($rawParticipants)
        ->map(function ($participant) {
            if ($participant instanceof \App\Models\WorkParticipant) {
                return [
                    'name' => $participant->participant_name,
                    'email' => $participant->email,
                    'phone' => $participant->phone,
                    'institution' => $participant->institution,
                    'role' => $participant->role,
                    'is_primary' => $participant->is_primary,
                ];
            }

            return [
                'name' => $participant['name'] ?? $participant['external_participant_name'] ?? null,
                'email' => $participant['email'] ?? null,
                'phone' => $participant['phone'] ?? null,
                'institution' => $participant['institution'] ?? null,
                'role' => $participant['role'] ?? null,
                'is_primary' => !empty($participant['is_primary']),
            ];
        })
        ->values()
        ->all();

    $nextParticipantIndex = count($normalizedParticipants);
@endphp

<div class="card card-outline card-secondary" id="participants-section" data-section>
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-users mr-1"></i>
            {{ __('2. Participantes y Responsables del Trabajo') }}
        </h3>
        <div class="card-tools">
            <span class="badge badge-info">{{ __('Obligatorio') }}</span>
        </div>
    </div>
    <div class="card-body">
        <p class="text-muted">
            {{ __('Registre al menos un participante responsable. Identifique datos de contacto completos para facilitar la coordinación y certificación del trabajo.') }}
        </p>

        <div id="participants-empty-state" class="text-center py-4 {{ $nextParticipantIndex > 0 ? 'd-none' : '' }}">
            <i class="fas fa-user-friends fa-2x text-muted mb-2"></i>
            <p class="text-muted mb-0">{{ __('Aún no se han agregado participantes.') }}</p>
        </div>

        <div id="participants-container" data-next-index="{{ $nextParticipantIndex }}">
            @foreach($normalizedParticipants as $index => $participant)
                @include('works.partials.participant-row', ['index' => $index, 'participant' => $participant])
            @endforeach
        </div>

        <button type="button" class="btn btn-outline-primary btn-sm" id="add-participant-btn">
            <i class="fas fa-user-plus"></i> {{ __('Agregar participante') }}
        </button>

        <template id="participant-row-template">
            @include('works.partials.participant-row', ['index' => '__INDEX__', 'participant' => []])
        </template>
    </div>
</div>
