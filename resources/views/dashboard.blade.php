{{-- dashboard.blade.php — 直接重導到紀錄列表，不再顯示此頁面 --}}
<?php redirect()->route('records.index')->send(); ?>
