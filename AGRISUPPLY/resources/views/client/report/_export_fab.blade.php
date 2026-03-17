<div style="position:fixed; right:24px; bottom:24px; display:flex; flex-direction:column; gap:12px; z-index:9999;">
    {{-- Print button --}}
    <button onclick="window.print()" class="fab fab-print" style="width:48px;height:48px;border-radius:50%;background:#2e7d32;display:flex;align-items:center;justify-content:center;color:#fff;text-decoration:none;box-shadow:0 4px 12px rgba(0,0,0,0.2);border:none;cursor:pointer;" title="Print">
        <i class="fa fa-print" style="font-size:16px"></i>
    </button>

    {{-- PDF button --}}
    <a href="{{ $pdfUrl }}" class="fab fab-pdf" style="width:48px;height:48px;border-radius:50%;background:#e53935;display:flex;align-items:center;justify-content:center;color:#fff;text-decoration:none;box-shadow:0 4px 12px rgba(0,0,0,0.2);" title="Export PDF">
        <i class="fa fa-file-pdf" style="font-size:18px"></i>
    </a>

    {{-- Excel button --}}
    <a href="{{ $excelUrl }}" class="fab fab-excel" style="width:48px;height:48px;border-radius:50%;background:#2e7d32;display:flex;align-items:center;justify-content:center;color:#fff;text-decoration:none;box-shadow:0 4px 12px rgba(0,0,0,0.2);" title="Export Excel">
        <i class="fa fa-file-excel" style="font-size:18px"></i>
    </a>
</div>
