{{-- resources/views/components/employee-worker-selector.blade.php --}}
@php
    $assignedIds = $lead->leadWorkers ? $lead->leadWorkers->pluck('employee_id')->toArray() : [];
@endphp

<div class="mb-4 employee-worker-selector" data-lead-id="{{ $lead->id ?? 'single' }}">
    {{-- Header with Counter & Bulk Actions --}}
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <label class="form-label fw-bold small text-muted mb-0 d-flex align-items-center gap-2">
            <i class="fas fa-user-plus text-success"></i>
            <span style="font-size: 0.85rem;">Select Employees / Workers <span class="text-danger">*</span></span>
        </label>
        <div class="d-flex align-items-center gap-2">
            <span class="badge worker-count-badge" 
                  style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; font-size: 0.74rem; padding: 5px 10px; border-radius: 8px;">
                <span class="worker-count-num">{{ count($assignedIds) }}</span> selected
            </span>
            <button type="button" class="btn btn-xs btn-outline-secondary btn-select-all" 
                    style="font-size: 0.72rem; padding: 4px 10px; border-radius: 8px;">
                Select All
            </button>
            <button type="button" class="btn btn-xs btn-outline-secondary btn-clear-all" 
                    style="font-size: 0.72rem; padding: 4px 10px; border-radius: 8px;">
                Clear
            </button>
        </div>
    </div>

    {{-- Instant Search Filter with comfortable padding --}}
    <div class="input-group mb-3 shadow-2xs" style="border-radius: 10px; overflow: hidden; border: 1px solid #e2e8f0;">
        <span class="input-group-text bg-white border-0 text-muted ps-3">
            <i class="fas fa-search" style="font-size: 0.82rem;"></i>
        </span>
        <input type="text" class="form-control border-0 ps-2 worker-search-input" 
               placeholder="Type to filter staff by name, code (e.g. DLK...), or skill (e.g. PHP, Python, AI)..." 
               style="font-size: 0.84rem; padding: 10px 14px; box-shadow: none;">
    </div>

    {{-- Selected Active Chips Tray with spacious breathing room --}}
    <div class="selected-chips-tray d-flex flex-wrap align-items-center gap-2 mb-3 p-2.5 rounded-3 border" 
         style="background: #f8fafc; border-color: #e2e8f0; min-height: 48px; max-height: 90px; overflow-y: auto;">
        @forelse($employees->whereIn('id', $assignedIds) as $emp)
            <span class="badge d-inline-flex align-items-center gap-2 active-worker-chip" 
                  data-emp-id="{{ $emp->id }}"
                  style="background: #10b981; color: #ffffff; font-size: 0.75rem; padding: 5px 10px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
                <span>{{ $emp->name }}</span>
                @if($emp->employee_code)
                    <span class="opacity-75 font-monospace" style="font-size: 0.68rem;">[{{ $emp->employee_code }}]</span>
                @endif
                <i class="fas fa-times remove-chip ms-1" style="cursor: pointer; font-size: 0.72rem;" title="Remove"></i>
            </span>
        @empty
            <span class="text-muted small empty-tray-hint px-1" style="font-size: 0.76rem;">
                <i class="far fa-hand-pointer me-1 text-success"></i> Click the <strong>+ Add</strong> button on any staff member below to assign them
            </span>
        @endforelse
    </div>

    {{-- Scrollable List with Spaced "ADD BUTTON STYLE" Cards --}}
    <div class="worker-card-list p-3 border rounded-3 bg-white" 
         style="max-height: 300px; overflow-y: auto; border-color: #e2e8f0;">
        <div class="row g-3 worker-grid">
            @foreach($employees as $emp)
                @php
                    $isSelected = in_array($emp->id, $assignedIds);
                    $searchData = strtolower($emp->name . ' ' . ($emp->employee_code ?? '') . ' ' . ($emp->department ?? '') . ' ' . ($emp->designation ?? ''));
                @endphp
                <div class="col-12 col-md-6 worker-col" data-search="{{ $searchData }}">
                    <div class="worker-card p-2.5 px-3 rounded-3 border d-flex align-items-center justify-content-between gap-3 {{ $isSelected ? 'is-selected' : '' }}"
                         data-id="{{ $emp->id }}"
                         data-name="{{ $emp->name }}"
                         data-code="{{ $emp->employee_code ?? '' }}"
                         style="cursor: pointer; transition: all 0.15s ease; {{ $isSelected ? 'background: #ecfdf5; border-color: #10b981 !important; box-shadow: 0 2px 8px rgba(16,185,129,0.18);' : 'background: #ffffff; border-color: #e2e8f0;' }}">
                        
                        {{-- Hidden real checkbox for form submission --}}
                        <input type="checkbox" name="employee_ids[]" value="{{ $emp->id }}" 
                               class="d-none worker-checkbox" {{ $isSelected ? 'checked' : '' }}>

                        <div class="d-flex align-items-center gap-2.5 text-truncate py-0.5">
                            {{-- Visual Avatar Circle --}}
                            <div class="worker-avatar rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-2xs" 
                                 style="width: 38px; height: 38px; flex-shrink: 0; font-size: 0.78rem; {{ $isSelected ? 'background: #10b981; color: #ffffff;' : 'background: #f1f5f9; color: #475569;' }}">
                                <span class="avatar-initials {{ $isSelected ? 'd-none' : '' }}">{{ strtoupper(substr($emp->name, 0, 2)) }}</span>
                                <i class="fas fa-check avatar-check {{ $isSelected ? '' : 'd-none' }}"></i>
                            </div>

                            <div class="text-truncate">
                                <div class="fw-bold text-dark text-truncate d-flex align-items-center gap-1.5 mb-0.5" style="font-size: 0.84rem;">
                                    <span class="text-truncate">{{ $emp->name }}</span>
                                    @if($emp->employee_code)
                                        <span class="badge font-monospace" style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; font-size: 0.64rem; padding: 2px 5px; border-radius: 4px;">
                                            {{ $emp->employee_code }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-muted text-truncate" style="font-size: 0.72rem;">
                                    {{ $emp->department ?? 'General' }} &bull; {{ $emp->designation ?? 'Staff' }}
                                </div>
                            </div>
                        </div>

                        {{-- ADD BUTTON STYLE Button --}}
                        <div class="flex-shrink-0">
                            <button type="button" class="btn btn-sm btn-worker-toggle {{ $isSelected ? 'btn-success text-white' : 'btn-outline-success' }}"
                                    style="border-radius: 9px; font-size: 0.74rem; padding: 5px 12px; font-weight: 600; min-width: 74px; {{ $isSelected ? 'color: #ffffff !important;' : '' }}">
                                <span class="lbl-add {{ $isSelected ? 'd-none' : '' }}"><i class="fas fa-plus me-1"></i> Add</span>
                                <span class="lbl-added {{ $isSelected ? '' : 'd-none' }} text-white" style="color: #ffffff !important;"><i class="fas fa-check me-1 text-white" style="color: #ffffff !important;"></i> Added</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="no-worker-match d-none text-center py-4 text-muted small">
            <i class="fas fa-search me-1"></i> No team members found matching your search.
        </div>
    </div>
    
    <div class="validation-msg text-danger small mt-2 d-none">
        <i class="fas fa-exclamation-circle me-1"></i> Please select at least one employee/worker to assign.
    </div>
</div>

<script>
(function() {
    if (window.__empWorkerSelectorLoaded) return;
    window.__empWorkerSelectorLoaded = true;

    function initSelector(container) {
        if (!container || container.dataset.selectorInitialized) return;
        container.dataset.selectorInitialized = 'true';

        const searchInput = container.querySelector('.worker-search-input');
        const items = container.querySelectorAll('.worker-col');
        const noMatch = container.querySelector('.no-worker-match');
        const chipsTray = container.querySelector('.selected-chips-tray');
        const countNum = container.querySelector('.worker-count-num');
        const selectAllBtn = container.querySelector('.btn-select-all');
        const clearAllBtn = container.querySelector('.btn-clear-all');
        const form = container.closest('form');
        const validationMsg = container.querySelector('.validation-msg');

        function updateTrayAndCounters() {
            let count = 0;
            const selectedEmployees = [];

            container.querySelectorAll('.worker-card').forEach(card => {
                const checkbox = card.querySelector('.worker-checkbox');
                const isSelected = checkbox.checked;
                const avatar = card.querySelector('.worker-avatar');
                const initials = card.querySelector('.avatar-initials');
                const checkIcon = card.querySelector('.avatar-check');
                const lblAdd = card.querySelector('.lbl-add');
                const lblAdded = card.querySelector('.lbl-added');
                const btnToggle = card.querySelector('.btn-worker-toggle');

                if (isSelected) {
                    count++;
                    card.classList.add('is-selected');
                    card.style.background = '#ecfdf5';
                    card.style.borderColor = '#10b981';
                    card.style.boxShadow = '0 2px 6px rgba(16,185,129,0.15)';
                    avatar.style.background = '#10b981';
                    avatar.style.color = '#ffffff';
                    if (initials) initials.classList.add('d-none');
                    if (checkIcon) checkIcon.classList.remove('d-none');
                    if (lblAdd) lblAdd.classList.add('d-none');
                    if (lblAdded) lblAdded.classList.remove('d-none');
                    if (btnToggle) {
                        btnToggle.classList.remove('btn-outline-success');
                        btnToggle.classList.add('btn-success', 'text-white');
                        btnToggle.style.color = '#ffffff';
                    }

                    selectedEmployees.push({
                        id: card.dataset.id,
                        name: card.dataset.name,
                        code: card.dataset.code
                    });
                } else {
                    card.classList.remove('is-selected');
                    card.style.background = '#ffffff';
                    card.style.borderColor = '#e2e8f0';
                    card.style.boxShadow = 'none';
                    avatar.style.background = '#f1f5f9';
                    avatar.style.color = '#475569';
                    if (initials) initials.classList.remove('d-none');
                    if (checkIcon) checkIcon.classList.add('d-none');
                    if (lblAdd) lblAdd.classList.remove('d-none');
                    if (lblAdded) lblAdded.classList.add('d-none');
                    if (btnToggle) {
                        btnToggle.classList.add('btn-outline-success');
                        btnToggle.classList.remove('btn-success', 'text-white');
                        btnToggle.style.color = '';
                    }
                }
            });

            if (countNum) countNum.textContent = count;
            if (validationMsg && count > 0) validationMsg.classList.add('d-none');

            // Render chips
            if (chipsTray) {
                if (selectedEmployees.length === 0) {
                    chipsTray.innerHTML = '<span class="text-muted small empty-tray-hint" style="font-size: 0.74rem;"><i class="far fa-hand-pointer me-1 text-success"></i> Click the <strong>+ Add</strong> button on team members below to assign them</span>';
                } else {
                    chipsTray.innerHTML = selectedEmployees.map(emp => `
                        <span class="badge d-inline-flex align-items-center gap-1.5 active-worker-chip" 
                              data-emp-id="${emp.id}"
                              style="background: #10b981; color: #ffffff; font-size: 0.74rem; padding: 4px 8px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                            <span>${emp.name}</span>
                            ${emp.code ? `<span class="opacity-75 font-monospace" style="font-size: 0.65rem;">[${emp.code}]</span>` : ''}
                            <i class="fas fa-times remove-chip" style="cursor: pointer; font-size: 0.72rem;" title="Remove"></i>
                        </span>
                    `).join('');

                    chipsTray.querySelectorAll('.remove-chip').forEach(btn => {
                        btn.addEventListener('click', function(e) {
                            e.stopPropagation();
                            const chip = this.closest('.active-worker-chip');
                            const empId = chip.dataset.empId;
                            const targetCard = container.querySelector(`.worker-card[data-id="${empId}"]`);
                            if (targetCard) {
                                const cb = targetCard.querySelector('.worker-checkbox');
                                if (cb) cb.checked = false;
                                updateTrayAndCounters();
                            }
                        });
                    });
                }
            }
        }

        // Click card or action button
        container.querySelectorAll('.worker-card').forEach(card => {
            card.addEventListener('click', function(e) {
                const cb = this.querySelector('.worker-checkbox');
                if (cb) {
                    cb.checked = !cb.checked;
                    updateTrayAndCounters();
                }
            });
        });

        // Search live filter
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                let matchCount = 0;
                items.forEach(item => {
                    const search = item.dataset.search || '';
                    if (!query || search.includes(query)) {
                        item.classList.remove('d-none');
                        matchCount++;
                    } else {
                        item.classList.add('d-none');
                    }
                });
                if (noMatch) {
                    if (matchCount === 0) noMatch.classList.remove('d-none');
                    else noMatch.classList.add('d-none');
                }
            });
        }

        // Select All Visible
        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', function() {
                items.forEach(item => {
                    if (!item.classList.contains('d-none')) {
                        const cb = item.querySelector('.worker-checkbox');
                        if (cb) cb.checked = true;
                    }
                });
                updateTrayAndCounters();
            });
        }

        // Clear All
        if (clearAllBtn) {
            clearAllBtn.addEventListener('click', function() {
                container.querySelectorAll('.worker-checkbox').forEach(cb => {
                    cb.checked = false;
                });
                updateTrayAndCounters();
            });
        }

        // Validation on form submit
        if (form) {
            form.addEventListener('submit', function(e) {
                const checked = container.querySelectorAll('.worker-checkbox:checked');
                if (checked.length === 0) {
                    e.preventDefault();
                    if (validationMsg) validationMsg.classList.remove('d-none');
                    container.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
        }

        // Run on init
        updateTrayAndCounters();
    }

    function initAll() {
        document.querySelectorAll('.employee-worker-selector').forEach(initSelector);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }

    // Also listen when bootstrap modals are shown
    document.addEventListener('shown.bs.modal', function(e) {
        if (e.target) {
            e.target.querySelectorAll('.employee-worker-selector').forEach(initSelector);
        }
    });
})();
</script>
