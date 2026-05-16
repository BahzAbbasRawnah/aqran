@extends('emails.layout', ['title' => 'إعادة تعيين كلمة المرور - أقران'])

@section('content')
    <h2 style="color: #0f172a; margin-top: 0;">طلب إعادة تعيين كلمة المرور</h2>
    <p>أهلاً {{ $name }}،</p>
    <p>لقد تلقينا طلباً لإعادة تعيين كلمة المرور الخاصة بحسابك في منصة أقران.</p>

    <p>يرجى استخدام رمز التحقق التالي لإكمال العملية:</p>

    <div class="otp-card">
        <h3 style="margin-bottom: 10px; color: #64748b; font-size: 14px;">رمز إعادة التعيين</h3>
        <p class="otp-code">{{ $code }}</p>
    </div>

    <p style="font-size: 14px; color: #64748b;">هذا الرمز صالح لمدة 10 دقائق فقط.</p>

    <div style="background-color: #fff7ed; border-right: 4px solid #f97316; padding: 15px; margin: 20px 0;">
        <p style="margin: 0; color: #9a3412; font-size: 14px;">
            <strong>ملاحظة أمنية:</strong> إذا لم تطلب هذا الرمز، يمكنك تجاهل هذا البريد بأمان. لن يتم إجراء أي تغييرات على
            حسابك.
        </p>
    </div>

    <p style="margin-bottom: 0;">مع تحيات،<br>فريق أمن أقران</p>
@endsection