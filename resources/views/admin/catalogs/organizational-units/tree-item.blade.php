<div class="tree-item" style="margin-left: {{ $level * 20 }}px;" data-unit-id="{{ $unit->id }}">
    <div class="d-flex align-items-center justify-content-between py-2 px-3 mb-2 border rounded bg-light">
        <div class="d-flex align-items-center">
            @if($unit->children->count() > 0)
                <i class="fas fa-minus-square tree-toggle text-primary mr-2" data-target="children-{{ $unit->id }}"></i>
            @else
                <i class="fas fa-circle text-muted mr-3" style="font-size: 0.5em; margin-left: 8px;"></i>
            @endif
            
            <div>
                <strong class="unit-name">{{ $unit->name }}</strong>
                <small class="text-muted ml-2">({{ $unit->code }})</small>
                <br>
                <small>
                    <span class="badge badge-{{ 
                        $unit->type === 'universidad' ? 'primary' : 
                        ($unit->type === 'facultad' ? 'success' : 
                        ($unit->type === 'centro' ? 'info' : 
                        ($unit->type === 'departamento' ? 'warning' : 'secondary'))) 
                    }}">
                        {{ ucfirst($unit->type) }}
                    </span>
                    
                    @if($unit->users->count() > 0)
                        <span class="badge badge-primary ml-1">{{ $unit->users->count() }} {{ __('usuarios') }}</span>
                    @endif
                    
                    @if(!$unit->is_active)
                        <span class="badge badge-secondary ml-1">{{ __('Inactivo') }}</span>
                    @endif
                </small>
            </div>
        </div>
        
        <div class="btn-group btn-group-sm">
            <button class="btn btn-outline-primary btn-sm" onclick="editUnit({{ $unit->id }})" title="{{ __('Editar') }}">
                <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-outline-info btn-sm" onclick="viewUnitDetails({{ $unit->id }})" title="{{ __('Ver detalles') }}">
                <i class="fas fa-eye"></i>
            </button>
            @if($unit->children->count() === 0 && $unit->users->count() === 0)
                <button class="btn btn-outline-danger btn-sm" onclick="deleteUnit({{ $unit->id }}, '{{ $unit->name }}')" title="{{ __('Eliminar') }}">
                    <i class="fas fa-trash"></i>
                </button>
            @else
                <button class="btn btn-outline-secondary btn-sm" disabled title="{{ __('No se puede eliminar: tiene dependencias') }}">
                    <i class="fas fa-lock"></i>
                </button>
            @endif
        </div>
    </div>
    
    @if($unit->children->count() > 0)
        <div id="children-{{ $unit->id }}" class="children">
            @foreach($unit->children->sortBy('name') as $child)
                @include('admin.catalogs.organizational-units.tree-item', ['unit' => $child, 'level' => $level + 1])
            @endforeach
        </div>
    @endif
</div>

@if($level === 0)
    @push('scripts')
    <script>
    $(document).ready(function() {
        // Toggle para expandir/contraer ramas del árbol
        $('.tree-toggle').on('click', function() {
            const target = $(this).data('target');
            const $children = $('#' + target);
            const $icon = $(this);
            
            if ($children.is(':visible')) {
                $children.slideUp();
                $icon.removeClass('fa-minus-square').addClass('fa-plus-square');
            } else {
                $children.slideDown();
                $icon.removeClass('fa-plus-square').addClass('fa-minus-square');
            }
        });
    });
    </script>
    @endpush
@endif