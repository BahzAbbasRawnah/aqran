<div class="p-4 bg-black rounded-xl flex items-center justify-center min-h-[320px]">
    @if($url)
        <video
            controls
            autoplay
            class="w-full max-h-[480px] rounded-lg shadow-2xl"
            src="{{ $url }}"
        >
            متصفحك لا يدعم تشغيل الفيديو.
        </video>
    @else
        <div class="text-center text-gray-400 py-16">
            <x-heroicon-o-video-camera class="w-16 h-16 mx-auto mb-4 opacity-40" />
            <p class="text-lg font-medium" dir="rtl">لا يوجد فيديو مرفق بهذا الطلب</p>
        </div>
    @endif
</div>
