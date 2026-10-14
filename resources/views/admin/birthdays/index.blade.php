{{-- resources/views/admin/birthdays/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Birthday & Anniversary')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-birthday-cake me-2 text-primary"></i>Birthday & Anniversary
            </h1>
            <p class="text-muted">Celebrate special occasions with your team</p>
        </div>
        <div>
            <button class="btn btn-primary" id="bulkBirthdayBtn" onclick="bulkSendWishes('birthday')">
                <i class="fas fa-gift me-1"></i> Send Birthday Wishes
            </button>
            <button class="btn btn-info" id="bulkAnniversaryBtn" onclick="bulkSendWishes('anniversary')">
                <i class="fas fa-heart me-1"></i> Send Anniversary Wishes
            </button>
        </div>
    </div>

    <!-- Today's Celebrations -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-gradient-pink text-white">
                    <i class="fas fa-birthday-cake me-2"></i> Today's Birthdays
                </div>
                <div class="card-body">
                    @if($todayBirthdays->count() > 0)
                        @foreach($todayBirthdays as $employee)
                        <div class="d-flex align-items-center mb-3 p-3 bg-light rounded" data-employee-id="{{ $employee->id }}">
                            <div class="avatar-lg me-3">
                                @if($employee->profile_photo)
                                    <img src="{{ Storage::url($employee->profile_photo) }}" 
                                         class="rounded-circle" 
                                         width="60" 
                                         height="60"
                                         loading="lazy"
                                         onerror="this.onerror=null; this.src='{{ asset('images/default-avatar.png') }}';"
                                         alt="{{ $employee->name }}">
                                @else
                                    <div class="bg-gradient-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <i class="fas fa-user fa-2x text-white"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-1">{{ $employee->name }}</h5>
                                <p class="mb-0 text-muted">{{ $employee->designation }} | {{ $employee->department }}</p>
                                @if($employee->age)
                                    <small class="text-success">🎂 Turning {{ $employee->age }} today!</small>
                                @endif
                            </div>
                            <button class="btn btn-sm btn-primary send-wish-btn" 
                                    onclick="sendWish({{ $employee->id }}, 'birthday')"
                                    aria-label="Send birthday wish to {{ $employee->name }}">
                                <i class="fas fa-gift" aria-hidden="true"></i>
                                <span class="visually-hidden">Send Wish</span>
                            </button>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <img src="{{ asset('images/empty-calendar.svg') }}" 
                                 alt="No birthdays" 
                                 class="mb-3" 
                                 width="80"
                                 style="opacity: 0.5;">
                            <p class="text-muted">No birthdays today</p>
                            <small class="text-muted">Check back tomorrow!</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-gradient-rose text-white">
                    <i class="fas fa-heart me-2"></i> Today's Anniversaries
                </div>
                <div class="card-body">
                    @if($todayAnniversaries->count() > 0)
                        @foreach($todayAnniversaries as $employee)
                        <div class="d-flex align-items-center mb-3 p-3 bg-light rounded">
                            <div class="avatar-lg me-3">
                                @if($employee->profile_photo)
                                    <img src="{{ Storage::url($employee->profile_photo) }}" 
                                         class="rounded-circle" 
                                         width="60" 
                                         height="60"
                                         loading="lazy"
                                         onerror="this.onerror=null; this.src='{{ asset('images/default-avatar.png') }}';"
                                         alt="{{ $employee->name }}">
                                @else
                                    <div class="bg-gradient-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <i class="fas fa-user fa-2x text-white"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-1">{{ $employee->name }}</h5>
                                <p class="mb-0 text-muted">{{ $employee->designation }} | {{ $employee->department }}</p>
                                <small class="text-success">💍 {{ $employee->years_of_service }} years with us!</small>
                            </div>
                            <button class="btn btn-sm btn-primary send-wish-btn" 
                                    onclick="sendWish({{ $employee->id }}, 'anniversary')"
                                    aria-label="Send anniversary wish to {{ $employee->name }}">
                                <i class="fas fa-gift" aria-hidden="true"></i>
                                <span class="visually-hidden">Send Wish</span>
                            </button>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <img src="{{ asset('images/empty-calendar.svg') }}" 
                                 alt="No anniversaries" 
                                 class="mb-3" 
                                 width="80"
                                 style="opacity: 0.5;">
                            <p class="text-muted">No anniversaries today</p>
                            <small class="text-muted">Check back tomorrow!</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Celebrations -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header">
                    <i class="fas fa-calendar-week me-2 text-primary"></i> Upcoming Birthdays (Next 7 Days)
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($upcomingBirthdays as $item)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong class="text-primary">{{ $item['date']->format('d M') }}</strong>
                                        <div class="mt-1">
                                            @foreach($item['employees'] as $employee)
                                                <div class="d-flex align-items-center mt-2">
                                                    <div class="avatar-sm me-2">
                                                        @if($employee->profile_photo)
                                                            <img src="{{ Storage::url($employee->profile_photo) }}" 
                                                                 class="rounded-circle" 
                                                                 width="30" 
                                                                 height="30"
                                                                 loading="lazy"
                                                                 onerror="this.onerror=null; this.src='{{ asset('images/default-avatar.png') }}';"
                                                                 alt="{{ $employee->name }}">
                                                        @else
                                                            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                                                <i class="fas fa-user fa-sm text-white"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <span class="fw-semibold">{{ $employee->name }}</span>
                                                        <small class="text-muted d-block">{{ $employee->designation }}</small>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <span class="badge bg-primary">
                                        {{ \Carbon\Carbon::parse($item['date'])->diffInDays(now()) }} days
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-center py-4">
                                <i class="fas fa-calendar-alt fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">No upcoming birthdays</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header">
                    <i class="fas fa-calendar-week me-2 text-primary"></i> Upcoming Anniversaries (Next 7 Days)
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($upcomingAnniversaries as $item)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong class="text-info">{{ $item['date']->format('d M') }}</strong>
                                        <div class="mt-1">
                                            @foreach($item['employees'] as $employee)
                                                <div class="d-flex align-items-center mt-2">
                                                    <div class="avatar-sm me-2">
                                                        @if($employee->profile_photo)
                                                            <img src="{{ Storage::url($employee->profile_photo) }}" 
                                                                 class="rounded-circle" 
                                                                 width="30" 
                                                                 height="30"
                                                                 loading="lazy"
                                                                 onerror="this.onerror=null; this.src='{{ asset('images/default-avatar.png') }}';"
                                                                 alt="{{ $employee->name }}">
                                                        @else
                                                            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                                                <i class="fas fa-user fa-sm text-white"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <span class="fw-semibold">{{ $employee->name }}</span>
                                                        <small class="text-muted d-block">{{ $employee->years_of_service }} years</small>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <span class="badge bg-info">
                                        {{ \Carbon\Carbon::parse($item['date'])->diffInDays(now()) }} days
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-center py-4">
                                <i class="fas fa-calendar-alt fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">No upcoming anniversaries</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Send Wish Modal -->
    <div class="modal fade" id="sendWishModal" tabindex="-1" aria-labelledby="sendWishModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-gradient-primary text-white">
                    <h5 class="modal-title" id="sendWishModalLabel">
                        <i class="fas fa-gift me-2"></i> Send Birthday/Anniversary Wish
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.birthdays.send-wish') }}" method="POST" id="sendWishForm">
                    @csrf
                    <input type="hidden" name="employee_id" id="wishEmployeeId">
                    <input type="hidden" name="type" id="wishType">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">
                                Personalized Message 
                                <span id="charCount" class="text-muted">0</span>
                                <span class="text-muted">/500</span>
                            </label>
                            <textarea name="message" 
                                      id="wishMessage" 
                                      class="form-control" 
                                      rows="4" 
                                      required 
                                      maxlength="500"
                                      placeholder="Write a personalized birthday/anniversary wish..."></textarea>
                            <div class="form-text">
                                <i class="fas fa-info-circle"></i> Your message will be sent via email to the employee.
                            </div>
                        </div>
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-envelope me-2"></i>
                            The wish will be sent immediately to the employee's email address.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="sendWishSubmitBtn">
                            <i class="fas fa-paper-plane me-1"></i> Send Wish
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Character counter for message input
function updateCharCount(textarea) {
    const count = textarea.value.length;
    const charCountSpan = document.getElementById('charCount');
    charCountSpan.textContent = count;
    
    // Change color based on character count
    if (count > 450) {
        charCountSpan.className = 'text-danger fw-bold';
    } else if (count > 400) {
        charCountSpan.className = 'text-warning fw-bold';
    } else {
        charCountSpan.className = 'text-muted';
    }
}

// Set loading state for buttons
function setLoadingState(button, isLoading) {
    if (isLoading) {
        button.disabled = true;
        button.dataset.originalHtml = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    } else {
        button.disabled = false;
        button.innerHTML = button.dataset.originalHtml;
    }
}

// Send individual wish
function sendWish(employeeId, type) {
    document.getElementById('wishEmployeeId').value = employeeId;
    document.getElementById('wishType').value = type;
    document.getElementById('wishMessage').value = '';
    document.getElementById('charCount').textContent = '0';
    
    // Update modal title based on type
    const modalTitle = document.getElementById('sendWishModalLabel');
    const titleText = type === 'birthday' ? 'Send Birthday Wish' : 'Send Anniversary Wish';
    modalTitle.innerHTML = `<i class="fas fa-gift me-2"></i> ${titleText}`;
    
    $('#sendWishModal').modal('show');
}

// Handle form submission with loading state
document.getElementById('sendWishForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('sendWishSubmitBtn');
    setLoadingState(submitBtn, true);
    
    // Form will submit normally, but we'll handle success/error via AJAX
    // This prevents double submission
    setTimeout(() => {
        setLoadingState(submitBtn, false);
    }, 3000);
});

// Bulk send wishes
function bulkSendWishes(type) {
    Swal.fire({
        title: `Send ${type === 'birthday' ? 'Birthday' : 'Anniversary'} Wishes`,
        text: `Send wishes to all employees celebrating ${type} today?`,
        icon: 'question',
        input: 'textarea',
        inputLabel: 'Personalized Message',
        inputPlaceholder: 'Write your message here...',
        inputAttributes: {
            'maxlength': '500',
            'aria-label': 'Type your message here'
        },
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-paper-plane"></i> Send All',
        cancelButtonText: 'Cancel',
        preConfirm: (message) => {
            if (!message || message.trim() === '') {
                Swal.showValidationMessage('Please enter a message');
                return false;
            }
            if (message.length > 500) {
                Swal.showValidationMessage('Message must not exceed 500 characters');
                return false;
            }
            return message.trim();
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state on buttons
            const birthdayBtn = document.getElementById('bulkBirthdayBtn');
            const anniversaryBtn = document.getElementById('bulkAnniversaryBtn');
            const buttons = [birthdayBtn, anniversaryBtn];
            
            buttons.forEach(btn => {
                if (btn) {
                    btn.disabled = true;
                    btn.dataset.originalHtml = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
                }
            });
            
            $.ajax({
                url: '{{ route("admin.birthdays.bulk-send") }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    type: type,
                    message: result.value
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    let errorMessage = 'Failed to send wishes';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire('Error!', errorMessage, 'error');
                },
                complete: function() {
                    // Reset button states
                    buttons.forEach(btn => {
                        if (btn) {
                            btn.disabled = false;
                            btn.innerHTML = btn.dataset.originalHtml;
                        }
                    });
                }
            });
        }
    });
}

// Add character counter to textarea
document.addEventListener('DOMContentLoaded', function() {
    const messageTextarea = document.getElementById('wishMessage');
    if (messageTextarea) {
        messageTextarea.addEventListener('input', function() {
            updateCharCount(this);
        });
    }
    
    // Add keyboard shortcut for modal (Ctrl+Enter to send)
    document.getElementById('sendWishForm')?.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            e.preventDefault();
            this.submit();
        }
    });
    
    // Add tooltips to buttons
    const sendButtons = document.querySelectorAll('.send-wish-btn');
    sendButtons.forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            const type = this.getAttribute('onclick')?.includes('birthday') ? 'birthday' : 'anniversary';
            this.setAttribute('title', `Send ${type} wish`);
        });
    });
});

// Add CSRF token meta tag if not present
if (!document.querySelector('meta[name="csrf-token"]')) {
    const meta = document.createElement('meta');
    meta.name = 'csrf-token';
    meta.content = '{{ csrf_token() }}';
    document.head.appendChild(meta);
}
</script>

<style>
    .bg-gradient-pink {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    .bg-gradient-rose {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .avatar-sm {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        overflow: hidden;
        flex-shrink: 0;
    }
    .avatar-lg {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        overflow: hidden;
        flex-shrink: 0;
    }
    
    /* Animation for new items */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .rounded {
        animation: fadeInUp 0.3s ease-out;
    }
    
    /* Hover effects */
    .card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    
    .list-group-item {
        transition: background-color 0.2s;
    }
    
    .list-group-item:hover {
        background-color: #f8f9fa;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .avatar-lg {
            width: 50px;
            height: 50px;
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
        
        .h3 {
            font-size: 1.5rem;
        }
    }
    
    /* Loading spinner animation */
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .fa-spinner {
        animation: spin 1s linear infinite;
    }
    
    /* Custom scrollbar */
    .list-group::-webkit-scrollbar {
        width: 6px;
    }
    
    .list-group::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }
    
    .list-group::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }
    
    .list-group::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    
    /* Focus styles for accessibility */
    .btn:focus-visible,
    button:focus-visible {
        outline: 2px solid #667eea;
        outline-offset: 2px;
    }
    
    /* Modal animation */
    .modal.fade .modal-dialog {
        transition: transform 0.3s ease-out;
    }
</style>
@endsection