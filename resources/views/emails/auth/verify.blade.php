@extends('emails.layout', ['title' => 'توثيق الحساب - أقران'])

@section('content')
    <h2 style="color: #0f172a; margin-top: 0;">أهلاً بك في أقران، {{ $name }}!</h2>
    <p>يسعدنا انضمامك إلينا في منصة أقران. نحن هنا لنساعدك في تحقيق أهدافك الأكاديمية بكل كفاءة وتميز.</p>

    <p>لإتمام عملية التسجيل وتفعيل حسابك، يرجى استخدام رمز التحقق (OTP) التالي:</p>

    <div class="otp-card">
        <h3 style="margin-bottom: 10px; color: #64748b; font-size: 14px;">رمز التوثيق</h3>
        <p class="otp-code">{{ $code }}</p>
    </div>

    <p style="font-size: 14px; color: #64748b;">هذا الرمز صالح لمدة 10 دقائق فقط. يرجى عدم مشاركته مع أي شخص آخر.</p>

    <p>بعد إدخال الرمز في التطبيق، ستتمكن من الوصول إلى كافة مميزات المنصة.</p>

    <div style="text-align: center;">
        <a href="{{ config('app.url') }}" class="btn">زيارة المنصة</a>
    </div>

    <p style="margin-bottom: 0;">شكراً لثقتك بنا،<br>فريق أقران</p>
@endsection