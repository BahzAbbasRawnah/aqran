<div class="p-4 bg-black rounded-xl flex items-center justify-center min-h-[320px]">
    @php
        $url = $getState();
        if ($url && !str_starts_with($url, 'http://') && !str_starts_with($url, 'https://')) {
            $url = \Illuminate\Support\Facades\Storage::disk('public')->url($url);
        }
    @endphp
    @if($url)
        <video
            controls
            class="w-full max-h-[480px] rounded-lg shadow-2xl"
            src="{{ $url }}"
        >
            متصفحك لا يدعم تشغيل الفيديو.
        </video>
    @else
        <div class="text-center text-gray-400 py-16">
            <x-heroicon-o-video-camera class="w-16 h-16 mx-auto mb-4 opacity-40" />
            <p class="text-lg font-medium" dir="rtl">لا يوجد فيديو مرفق</p>
        </div>
    @endif
</div>
