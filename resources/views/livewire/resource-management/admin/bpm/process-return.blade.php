<<<<<<< HEAD
<div>
    @section('title', __('Proses Pemulangan Peralatan untuk Permohonan #') . $loanApplication->id)

    <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
        <h4 class="fw-bold mb-0 d-flex align-items-center">
            <i class="bi bi-box-arrow-in-down-right me-2"></i>
            @lang('Rekod Pemulangan Peralatan')
            <span class="badge bg-label-primary ms-2">@lang('Untuk Permohonan') #{{ $loanApplication->id }}</span>
        </h4>
    </div>

    @include('_partials._alerts.alert-general')

    {{-- Details of the original issuance --}}
    <div class="card motac-card mb-4">
        <div class="card-header motac-card-header">
            <h5 class="card-title mb-0">@lang('Butiran Pengeluaran Asal (Transaksi #:id)', ['id' => $issueTransaction->id])</h5>
        </div>
        <div class="card-body">
            <p><span class="fw-semibold">@lang('Dikeluarkan kepada'):</span> {{ $issueTransaction->receivingOfficer->name ?? 'N/A' }}</p>
            <p><span class="fw-semibold">@lang('Pada'):</span> {{ $issueTransaction->issue_timestamp?->translatedFormat('d M Y, g:i A') ?? 'N/A' }}</p>
        </div>
    </div>

    <form wire:submit.prevent="submitReturn">
        <div class="card motac-card">
            <div class="card-header motac-card-header">
                <h5 class="card-title mb-0">@lang('Pemeriksaan Item Semasa Pemulangan')</h5>
            </div>
            <div class="card-body">
                @error('returnItems') <div class="alert alert-danger">{{ $message }}</div> @enderror

                @forelse($returnItems as $index => $item)
                    <div wire:key="return-item-{{ $item['loan_transaction_item_id'] }}" class="border rounded p-3 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" wire:model.live="returnItems.{{ $index }}.is_returning" id="return_item_{{ $index }}">
                            <label class="form-check-label fw-semibold" for="return_item_{{ $index }}">
                                {{ $item['equipment_name'] }}
                            </label>
                        </div>

                        @if($returnItems[$index]['is_returning'])
                            <div class="ps-4 mt-3 border-start">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="condition_item_{{ $index }}" class="form-label">@lang('Keadaan') <span class="text-danger">*</span></label>
                                        <select wire:model="returnItems.{{ $index }}.condition_on_return" id="condition_item_{{ $index }}" class="form-select @error('returnItems.'.$index.'.condition_on_return') is-invalid @enderror">
                                            @foreach($conditionOptions as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('returnItems.'.$index.'.condition_on_return') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="notes_item_{{ $index }}" class="form-label">@lang('Catatan')</label>
                                        <input type="text" wire:model="returnItems.{{ $index }}.return_item_notes" id="notes_item_{{ $index }}" class="form-control" placeholder="@lang('cth: Terdapat calar kecil')">
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="alert alert-info">@lang('Semua peralatan dari transaksi ini telah dipulangkan.')</div>
                @endforelse

                <hr class="my-4">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="returning_officer_id" class="form-label fw-semibold">@lang('Peralatan Dipulangkan Oleh') <span class="text-danger">*</span></label>
                        <select wire:model="returning_officer_id" id="returning_officer_id" class="form-select @error('returning_officer_id') is-invalid @enderror">
                             {{-- CORRECTED: Use array syntax to access properties --}}
                             @foreach($users as $user)
                                <option value="{{ $user['id'] }}">{{ $user['name'] }}</option>
                            @endforeach
                        </select>
                         @error('returning_officer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="transaction_date" class="form-label fw-semibold">@lang('Tarikh Pemulangan') <span class="text-danger">*</span></label>
                        <input type="date" wire:model="transaction_date" id="transaction_date" class="form-control @error('transaction_date') is-invalid @enderror">
                        @error('transaction_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                 <div class="mb-3">
                    <label for="return_notes" class="form-label fw-semibold">@lang('Catatan Keseluruhan Pemulangan')</label>
                    <textarea wire:model="return_notes" id="return_notes" class="form-control" rows="3"></textarea>
                </div>
                 <p class="form-text">
                    @lang('Diterima Oleh (Pegawai BPM)'): {{ Auth::user()->name }}
                </p>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('loan-applications.show', $loanApplication->id) }}" class="btn btn-secondary me-2">@lang('Batal')</a>
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove><i class="bi bi-check-lg me-1"></i> @lang('Sahkan Pemulangan')</span>
                    <span wire:loading><span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> @lang('Memproses...')</span>
                </button>
            </div>
        </div>
    </form>
</div>
=======
{{-- resources/views/livewire/resource-management/admin/bpm/process-return.blade.php --}}
 <div>
     <h2 class="text-2xl font-bold mb-6 text-gray-800">Rekod Pulangan Peralatan untuk Permohonan Pinjaman #{{ $loanApplication->id }}</h2>

     @if (session()->has('success'))
         <div class="alert alert-success">{{ session('success') }}</div>
     @endif
     @if (session()->has('error'))
         <div class="alert alert-danger">{{ session('error') }}</div>
     @endif
     @if ($errors->any())
         <div class="alert alert-danger mb-4">
             <p class="font-semibold">Sila perbetulkan ralat berikut:</p>
             <ul class="list-disc list-inside">
                 @foreach ($errors->all() as $error)
                     <li>{{ $error }}</li>
                 @endforeach
             </ul>
         </div>
     @endif

     {{-- Loan Application Details Card (similar to return.blade.php) --}}
     <div class="card mb-6">
         <h3 class="card-title">Butiran Permohonan Pinjaman</h3>
         <p><span class="font-semibold">Pemohon:</span> {{ $loanApplication->user->name ?? 'N/A' }}</p>
         {{-- Other loan application details --}}

         @if ($issuedTransactionItems->isNotEmpty())
             <h4 class="text-lg font-semibold mt-4 mb-2 text-gray-700">Peralatan Sedang Dipinjam Untuk Permohonan Ini:</h4>
             <div class="overflow-x-auto shadow-sm rounded-md border border-gray-200">
                 <table class="min-w-full divide-y divide-gray-200 table">
                     <thead class="bg-gray-50">
                         <tr>
                             <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Peralatan (Tag ID)</th>
                             <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Tarikh Dikeluarkan</th>
                             {{-- Add other relevant columns from return.blade.php --}}
                         </tr>
                     </thead>
                     <tbody class="bg-white divide-y divide-gray-200">
                         @foreach ($issuedTransactionItems as $item)
                         <tr>
                             <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-b">
                                 {{ $item->equipment->brand ?? 'N/A' }} {{ $item->equipment->model ?? 'N/A' }}
                                 (Tag: {{ $item->equipment->tag_id ?? 'N/A' }})
                             </td>
                             <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-b">
                                 {{ $item->loanTransaction->issue_timestamp?->format('Y-m-d H:i') ?? $item->loanTransaction->transaction_date?->format('Y-m-d H:i') ?? 'N/A' }}
                             </td>
                             {{-- other columns --}}
                         </tr>
                         @endforeach
                     </tbody>
                 </table>
             </div>
         @else
             <p class="text-gray-600 italic">Tiada peralatan sedang dipinjam untuk permohonan ini.</p>
         @endif
     </div>

     <form wire:submit.prevent="submitReturn">
         <div class="card">
             <h3 class="card-title">Rekod Pulangan Peralatan</h3>

             <div class="form-group">
                 <label for="selectedTransactionItemIds" class="block text-gray-700 text-sm font-bold mb-2">Pilih Peralatan yang Dipulangkan*:</label>
                 <select wire:model.defer="selectedTransactionItemIds" id="selectedTransactionItemIds" class="form-control @error('selectedTransactionItemIds') border-red-500 @enderror" multiple required>
                     @forelse ($issuedTransactionItems as $item)
                         <option value="{{ $item->id }}">
                             {{ $item->equipment->brand ?? 'N/A' }} {{ $item->equipment->model ?? 'N/A' }}
                             (Tag: {{ $item->equipment->tag_id ?? 'N/A' }})
                             - Dikeluarkan: {{ $item->loanTransaction->issue_timestamp?->format('Y-m-d H:i') ?? $item->loanTransaction->transaction_date?->format('Y-m-d H:i') ?? 'N/A' }}
                         </option>
                     @empty
                          <option value="" disabled>Tiada peralatan untuk dipulangkan.</option>
                     @endforelse
                 </select>
                 @error('selectedTransactionItemIds') <span class="text-danger">{{ $message }}</span> @enderror
             </div>

             {{-- If tracking condition per item explicitly in the form --}}
             {{-- @foreach($issuedTransactionItems as $item)
                 @if(in_array($item->id, $selectedTransactionItemIds))
                     <div class="form-group">
                         <label for="condition_item_{{$item->id}}" class="block text-gray-700 text-sm font-bold mb-2">Keadaan Semasa Pulangan untuk {{ $item->equipment->tag_id }}:</label>
                         <select wire:model.defer="item_conditions.{{$item->id}}" id="condition_item_{{$item->id}}" class="form-control">
                             @foreach(App\Models\Equipment::getConditionStatusOptions() as $value => $label)
                                 <option value="{{ $value }}">{{ $label }}</option>
                             @endforeach
                         </select>
                     </div>
                 @endif
             @endforeach --}}


             <div class="form-group">
                 <label class="block text-gray-700 text-sm font-bold mb-2">Senarai Semak Aksesori Dipulangkan:</label>
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                     @foreach ($allAccessoriesList as $accessory)
                         <div class="flex items-center">
                             <input type="checkbox" wire:model.defer="accessories_on_return" value="{{ $accessory }}" id="return-accessory-{{ Str::slug($accessory) }}" class="form-check-input h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                             <label class="ml-2 block text-sm text-gray-700" for="return-accessory-{{ Str::slug($accessory) }}">{{ $accessory }}</label>
                         </div>
                     @endforeach
                 </div>
                  @error('accessories_on_return') <span class="text-danger">{{ $message }}</span> @enderror
             </div>

             <div class="form-group">
                 <label for="return_notes" class="block text-gray-700 text-sm font-bold mb-2">Catatan Pulangan (cth: kerosakan, item hilang):</label>
                 <textarea wire:model.defer="return_notes" id="return_notes" class="form-control @error('return_notes') border-red-500 @enderror" rows="3"></textarea>
                 @error('return_notes') <span class="text-danger">{{ $message }}</span> @enderror
             </div>

             <div class="form-group">
                 <label class="block text-gray-700 text-sm font-bold mb-1">Diterima Oleh:</label>
                 <p class="text-gray-800">{{ Auth::user()->name ?? 'N/A' }}</p>
             </div>
         </div>

         <div class="flex justify-center mt-6">
             <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                  <svg wire:loading wire:target="submitReturn" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span wire:loading.remove wire:target="submitReturn">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </span>
                 Rekod Pulangan Peralatan
             </button>
         </div>
     </form>

     <div class="mt-6 text-center">
          <a href="{{ route('resource-management.my-applications.loan-applications.show', $loanApplication) }}" class="btn btn-secondary"> {{-- --}}
             <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
             </svg>
             Kembali ke Butiran Permohonan
         </a>
     </div>
 </div>
>>>>>>> 7940bed (feat: Standardize authorization policies, update service provider and models, and refine configuration for consistent role management and grade-based approvals; Refactor: Streamline notification system with generic classes and consolidations)
